# nginx-main-conf role for Ansible
Install a main conf file that listens to *.subconf files for partial configurations

## Set up
- Add this role and eventually it's dependencies in your `roles` folder as git submodule:
`git submodule add git://github.com/davinov/ansible-nginx-main-conf [path-to-roles]/nginx-main-conf`
- Add it to your playbook's roles if it's not required by another one

## New site
To add a new site, add the nginx configuration to templates/etc/nginx/conf.d and then add this configuration to [the main task](tasks/main.yml).

## Variables
`domain` default to `_`
