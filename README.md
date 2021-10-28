
## Secture 360

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>
    Laravel Version - 8
<p align="center">

</p>


## Installation

- Checkout the repository
- Install Docker 
  - Follow instruction at https://docs.docker.com/engine/install/ubuntu/
    - `sudo apt-get remove docker docker-engine docker.io containerd runc`
    - `sudo apt-get update`
    - ```
      sudo apt-get install \
      ca-certificates \
      curl \
      gnupg \
      lsb-release 
      ```
    - ```
      curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
      ```
    - ```
      echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
      $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
      ```
    - ``` 
      sudo apt-get update
      sudo apt-get install docker-ce docker-ce-cli containerd.io
      ```
- Navigate to repository directory
- Run following to initiate working directory      
    ```
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs
    ```
- Configure env
## Start server 
  - `./vendor/bin/sail up`
  - To set sail variable, use `alias sail='bash vendor/bin/sail'`, then `sail up`

  
