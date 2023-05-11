![Docker Pulls](https://img.shields.io/docker/pulls/d3fk/asciinematic) ![Docker Cloud Automated build](https://img.shields.io/docker/cloud/automated/d3fk/asciinematic) ![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/d3fk/asciinematic) ![Docker Image Size (latest by date)](https://img.shields.io/docker/image-size/d3fk/asciinematic) ![Docker Stars](https://img.shields.io/docker/stars/d3fk/asciinematic) [![GitHub license](https://img.shields.io/github/license/Angatar/asciinematic)](https://github.com/Angatar/asciinematic/blob/master/LICENSE)
# asciinematic (Angatar> d3fk/asciinematic)

This is the second container made to serve as training material for training classes on Docker (ProDev, CESAR, CNRS DR12) with almost no code (text/ascii files are mainly manipulated).
NB: if you are looking for the first part of this training material, see d3fk/whalecome.

It contains a small website that displays animated ascii content using the SSE (Server-Sent Events) technology with PHP.

Through the use of this container in its 2 versions (see available tags) the user/student is invited to understand:
- How to map the container ports to the host's ports
- The non persistence of the container content
- The use of volumes and bind mounts to make any content to persist
- The use of docker sub-networks
- The use of docker network to create communication between containers
- The use of bind mounts and volumes to share content between containers
- The docker-compose basic commands on CLI
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

Change the DEFAULT_STREAM value from "asciistream" to "DR12" (:wq to save and quit)

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
But if instead we remove the container and run d3fk/asciinematic again, a new container will be created and the change will disappear... it is due to the immutability of the image of the container
```sh
docker rm -f asciinematic
```
```sh
docker run -p 80:80 -d --name asciinematic --restart always d3fk/asciinematic
```
Visit the website and observe that the change you made earlier has disapeared... the vim package is even no more present in the container.

4. **Use a bind mount to make a change more persistent e.g: including new containers if they use the same bind mount**
As asked by the stickman, we need to create a bind mount but we don't have the sources of the container, we only know it is in /var/www/html
  - We first need to get the index.php file ... from our running container for example
  ```sh
  docker cp asciinematic:/var/www/html/index.php .
  ```
  done!

  - Lets now create a new container with the bind mount ... docker do not accept 2 containers with the same name nor 2 containers trying to allocate the same port on the host
  ```sh
  docker run -p 90:80 -d --name asciinematic2 -v $(pwd)/index.php:/var/www/html/index.php --restart always d3fk/asciinematic
  ```
  Note: in PowerShell replace `$(pwd)/` by `${pwd}\`

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
The d3fk/asciinematic was a good example of a simple web container but it is Debian based, so a bit heavy, containing more than required ... lets try a lighter version based on Alpine Linux d3fk/asciinematic:fpm

fpm stands for Fast CGI Process Manager ... known to be more robust in terms of load than the standard PHP version.
It basically serves php processed script to the port 9000 by default and only php files ... moreover it is very confident, and can't be safely exposed to the world so we need to proxy it
The solution will be to use another container that will be used as proxy  of the fpm application and serve static files in complement to the php-fpm

on the docker default network we can use the --link option of the docker run command to connect 2 containers on the network (/etc/hosts is maintained up to date by docker engine on the linked containers)
```sh
docker run --link option
 ```
 We could also makes use of the docker DNS capabilities available on custom networks
 - Starting by creating a new network
  ```sh
  docker network create app1
  ```
  - Then running our fpm application in this custom network
  ```sh
  docker run --network app1 --name fpm-app -d d3fk/asciinematic:fpm
  ```
  Note: we don't need to open any port here our container expose port 9000 on the local network as defined in its base image
   - Then running our nginx server also in this custom network
  ```sh
  docker run -p 8080:80 -d --name nginx --network app1 d3fk/nginx-fpm:sse
  ```
  Note: d3fk/nginx-fpm is configured to serve by default any fpm app named fpm-app on the same network on port 9000 the name of the served app can be change by using ENV.
  The :sse tag is a version of nginx-fpm dedicated to sse that require nginx to not use cache and not create bunches of packets.
  Visit  [your localhost on port 8080](http://localhost:8080)
  What happened?
  Answer: Your proxy can serve the fpm-app but not the static files ... we need to mount the static files to nginx so that it can serve them
  ... lets use a volume for that purpose ...
  - Violently remove all containers:
  ```sh
  docker rm -f $(docker ps -aq)
  ```
  - Recreate our fpm with a volume, let simply name it "data" (observe volume propagation property)
  ```sh
  docker run --network app1 --name fpm-app -d -v data:/var/www/html/ d3fk/asciinematic:fpm
  ```
  Note: a new volume is created (data) as it was empty it now contains the content of "/var/www/html/", if it had content it would have replaced "/var/www/html/"... it is the property of data propagation of the docker volumes
  The volume can be seen from
  ```sh
  docker volume ls
  ```
  - Recreate our nginx container using the data volume mounted on the same path
  ```sh
  docker run -p 8080:80 -d --name nginx --network app1 -v data:/var/www/html/ d3fk/nginx-fpm:sse
  ```
  Does it works now? ;)
  - **Wait a minute! we forgot to inspect our images, it seems a volume was automatically created by the fpm-app**
  ```sh
  docker inspect d3fk/asciinematic:fpm
  ```
  So lets clean all again and retry with the volume that exists:
  ```sh
  docker rm -f $(docker ps -aq)
  docker volume prune -f
  ```
  Recreate our fpm as initially
  ```sh
  docker run --network app1 --name fpm-app -d d3fk/asciinematic:fpm
  ```
  Then recreate nginx-fpm:sse using the unnamed volume automatically generated by our fpm-app
  ```sh
  docker run -p 8181:80 --network app1 -d --volumes-from fpm-app d3fk/nginx-fpm:sse
  ```
  Note: the port was changed jsut to see if you are paying attention
  Note2: the --volumes-from option allows to mount all volumes of another container on the same paths

  Visit  [your localhost on port 8181](http://localhost:8181)

  Great! you made it works again ... but, let say it, it is quite annoying to type all these commands at every run, isn't it?

6. **Running a multi-containerized application with docker-compose**
- Lets clean a new time
  ```sh
  docker rm -f $(docker ps -aq)
  docker volume prune -f
  ```
- An existing docker-compose.yaml is available on the fpm branch of the d3fk/asciinematic repo
  We are going to download/copy it (use your favourite way, including alpine/git container)
  Then simply start the file with
  ```sh
  docker-compose up -d
  ```
  visit your localhost at port 80 ... does it works?
  More easy to deploy, isn’t it?
  We can still access each container by using standard docker commands but there are also some docker-compose commands e.g:
  ```sh
  docker-compose logs -f
  ```
  To shut down the composed application, simply use
  ```sh
  docker-compose down
  ```
  To remove stopped containers
  ```sh
  docker-compose rm
  ```
  In case you need anonymous volume are recreated on run (e.g: to avoid persistence) you can make use of the -V option
  ```sh
  docker-compose up -d -V
  ```

... to be continued

  docker-compose can be used to deploy on Swarm ;)
----
## CONGRATULATION!
You are ready for the next level :)

= Orchestration ... maybe with Docker Swarm in a first time?



[![GitHub license](https://img.shields.io/github/license/Angatar/asciinematic)](https://github.com/Angatar/asciinematic/blob/master/LICENSE)

----
----
## HIDDEN MEDIA CREDITS:
*Many thanks to the artists who accepted to let me embed their piece of art into this container, they made this docker training lesson much more entertaining for the students, they will for sure remind more of it:*

- **STARWARS ASCIIMATION:** by Simon Jansen ([official website https://www.asciimation.co.nz/](https://www.asciimation.co.nz/))

- **Music credit:** Star Wars theme by Mos Eisley Kazoo Orchestra (music by John Williams, executive producers Roberto Ferreri & Giuseppe D'Agostino)

***Any reuse of these art materials outside of one the d3fk/asciinematic container image has to be granted by the artists first!***

