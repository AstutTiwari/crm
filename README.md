# CRM

Steps setup at local server:
1. git clone
2. git checkout master branch
3. run command i.e composer install
4. run command i.e npm install (Note: if you use shared hosting then you have to make build by 'npm run dev' command on localhost and push code on server)
5. run command i.e composer dump-autoload
6. ask project lead for .env file
7. create database i.e crm
8. run command i.e php artisan migrate
9. run command i.e php artisan db:seed command for import dummy data or rquired data for run the project
10.run storage link command i.e php artisan storage:link
