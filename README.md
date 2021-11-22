![Docker Pulls](https://img.shields.io/docker/pulls/d3fk/asciinematic) ![Docker Cloud Automated build](https://img.shields.io/docker/cloud/automated/d3fk/asciinematic) ![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/d3fk/asciinematic) ![Docker Image Size (latest by date)](https://img.shields.io/docker/image-size/d3fk/asciinematic) ![Docker Stars](https://img.shields.io/docker/stars/d3fk/asciinematic) [![GitHub license](https://img.shields.io/github/license/Angatar/asciinematic)](https://github.com/Angatar/asciinematic/blob/master/LICENSE)
# asciinematic (Angatar> d3fk/asciinematic)

This is the second container made to serve as training material for a "ProDev" training class on Docker with almost no code (text/ascii files are mainly manipulated).
NB: if you are looking for the first part of this training material, see d3fk/whalecome.

It contains a small website that displays animated ascii content using the SSE (Server-Sent Events) technology with PHP.

Through the use of this container in its 2 versions (cf available tags) the user/student is invited to understand:
- How to map the container ports to the host's ports
- The non persistance of the container content
- The use of volumes and bind mounts to make any content to persist
- The use of docker sub-networks
- The use of docker network to create communication between containers
- The use of bind mounts and volumes to share content between containers
- The docker-compose basic commnands on CLI
- How to create his own docker-compose.yml for


## Docker image
pre-build from Docker hub with "automated build" option.

image name **d3fk/asciinematic**

`docker pull d3fk/asciinematic`

Docker hub repository: https://hub.docker.com/r/d3fk/asciinematic/


## Available tags
- **d3fk/asciinematic:latest** this image makes use of php-apache debian to simply serve content from a single container
- **d3fk/asciinematic:master** this image is simply another tag for **d3fk/asciinematic:latest**, they are identical images
- **d3fk/asciinematic:fpm** this image makes use of php-fpm-alpine (simple config) to make the user combine several containers


## Basic usage

```sh
docker run -p 80:80 d3fk/asciinematic
```

## Initiation to Docker: learning steps

0. **Starting point**

  - **Analyse how the image was created**
  ```sh
  docker history d3fk/asciinematic
  ```
Note: Shows that the image basically configures PHP and Apache2 && exposes port 80

1. **Run this container that need to open a port on the host to be accessed through http**
```sh
docker run -p 80:80 -d --name asciinematic --restart always d3fk/asciinematic
```
Note: the first 80 corresponds to the Host's port and it is mapped to :80 of the container that is exposed.
You can map any port of the host to the exposed ports of the container (tcp or udp, default is tcp but 80:80\udp would precise to use udp)

Note2: you can see that we are also using the **-d** detach option to keep control on our terminal

Note3: you can also see that we have introduced another option **--restart** (value can be set to no, on-failure, always, unless-stopped) as we have a website here we need it to be up except if we manually say it to stop.

2. **View our website running and access container log**
As d3fk/asciinematic is now running you can access to the website deployed from [your localhost](http://localhost)

It serves ascii stream using event-stream and Server-Sent Events.

If you need to see your container logs (-f is optional and serve to follow changes)
```sh
docker logs -f asciinematic
```
Note: the -f option serves to maintain connection and follow changes

Note2: By default, docker logs shows the command’s STDOUT and STDERR

If you need your container to forward request and error logs to docker log collector you can simply do it with a symbolic link defined in the Dockerfile of your image. As an example the Nginx official image is doing the following in its Dockerfile:
```dockerfile
RUN ....................... \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log \
```

3. **Enter into the running container and edit a file of the container (observe the container image immutability)**
Sometimes it may be usefull to break into a container to make a test/POC

```sh
docker exec -ti asciinematic bash
```
You are now into the running container(we can use bash as it is Debian based) ... we are going to make a slight change in the index.php file

```sh
$ apt-get update
$ apt-get install -y vim
$ vi index.php
```

Change the DEFAULT_STREAM value from "asciistream" to "ProDev" (:wq to save and quit)

type ```exit``` to leave the container

... visit your localhost and watch the effect of the change you made to the container.

Please, for now, just note, for later, the value the stickman gives to you :)

You can observe that the last change you've made to the container will be available until the container is changed for a new one.

e.g: reload the page; stop and start the container ... it is still the same container it contains the same value you changed.


note the asciinematic container ID:

```sh
docker ps
```

```sh
docker exec -it asciinematic ps -ef
docker exec -it asciinematic kill 1
```
observe the result on the container (the ps can be of course run several times)

```sh
docker ps
```
You should observe that your container disapeared but it was automatically restarted(same container ID but STATUS Up X seconds) by Docker to replace the one with the process killed (since we've asked Docker to always restart this container unless we stop it)

The website still have the change you made.
But if instead we remove the container and run d3fk/asciinematic again, a new container will be created and the change will disapear... it is due to the immutability of the image of the container
```sh
docker rm -f asciinematic
```
```sh
docker run -p 80:80 -d --name asciinematic --restart always d3fk/asciinematic
```
Visit the website and observe that the change you made earlier has disapeared... the vim package is even no more present in the container.

4. **Use a bind mount to make a change more persistent e.g: including new containers if they use the same bind mount **
As asked by the stickman, we need to create a bind mount but we don't have the sources of the container, we only know it is in /var/www/html
  - We first need to get the index.php file ... from our running container for example
  ```sh
  docker cp asciinematic:/var/www/html/index.php .
  ```
  done!

  - Lets now create a new container with the bind mount ... docker do not accept 2 containers with the same name nor 2 containers trying to allocate the same port on the host
  ```sh
  docker run -p 90:80 -d --name asciinematic2 -v $(pwd)/index.php:/var/www/index.php --restart always d3fk/asciinematic
  ```
  Note: in PowerShell replace $(pwd)/ by ${pwd}\

  Visit [your localhost on port 80](http://localhost) and [your localhost on port 90](http://localhost:90) to observe they are identical

  - We only have now to edit our local index.php file.. we could also use a bind mount for that:
  ```sh
  docker run -v $(pwd):/tmp -w /tmp -it --rm busybox vi index.php
  ```
  Change the DEFAULT_STREAM value from "asciistream" to "jedi" as recommended by the stickman (:wq to save and quit)

  Visit [your localhost on port 80](http://localhost) and [your localhost on port 90](http://localhost:90) to observe they are different

  Removing the asciinematic2 container to create a new one with the same bind mount will also contain the change you made.


  . . .
  Lets now remove our running containers to move with a new tag ...
  ```sh
  docker rm -f asciinematic asciinematic2
  ```

5. **Running a multi-containerized application with Docker cli**
The d3fk/asciinematic was a good example of a simple web container but it is Debian based, so a bit heavy, containning more than required ... lets try a lighter version d3fk/asciinematic:fpm


... to be continued
----
## CONGRATULATION!
You are ready for the next level :)

Docker Swarm ?



[![GitHub license](https://img.shields.io/github/license/Angatar/asciinematic)](https://github.com/Angatar/asciinematic/blob/master/LICENSE)

