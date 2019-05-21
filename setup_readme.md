>>>>> Start Instructions <<<<<<<

git clone https://github.com/fraba/sns-experiment.git
cd sns-experiment
mkdir uploads/csv
sudo chown -R www-data:www-data *
sudo mysql

## you are now in the mysql command prompt
create database sns;
grant all privileges on *.* to 'sns'@'localhost' identified by 'thisismypassword' with grant option;
use sns;

CREATE TABLE IF NOT EXISTS `_user_surveys` (
`user_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
`pol_op` int(11) NOT NULL,
`pol_op_abo` int(11) NOT NULL,
`pol_op_imm` int(11) NOT NULL,
`pol_op_gay` int(11) NOT NULL,
`pol_op_eco` int(11) NOT NULL,
`int_abo_sur` int(11) NOT NULL,
`int_gay_sur` int(11) NOT NULL,
`int_eco_sur` int(11) NOT NULL,
`int_imm_sur` int(11) NOT NULL,
`int_abo_obs` int(11) NOT NULL,
`int_gay_obs` int(11) NOT NULL,
`int_eco_obs` int(11) NOT NULL,
`int_imm_obs` int(11) NOT NULL,
PRIMARY KEY (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM `_user_surveys`;
INSERT INTO `_user_surveys` (`user_email`, `pol_op`, `pol_op_abo`, `pol_op_imm`, `pol_op_gay`, `pol_op_eco`, `int_abo_sur`, `int_gay_sur`, `int_eco_sur`, `int_imm_sur`, `int_abo_obs`, `int_gay_obs`, `int_eco_obs`, `int_imm_obs`) VALUES
('somebody@somemail.com', 1, 1, 2, 2, 3, 3, 5, 5, 4, 1, 2, 3, 4),
('another@somemail.com', 1, 1, 2, 2, 3, 3, 5, 5, 4, 4, 3, 2, 1),
('andathird@somemail.com', 1, 1, 2, 2, 3, 3, 5, 5, 4, 1, 2, 3, 4);

quit

## you have now exited the mysql command prompt and are back at the Linux command prompt

sudo su
sudo curl -s https://getcomposer.org/installer | php ## i did not test this step because composer is already installed on my server

sudo -u www-data composer install # This can generate errors if php modules are not installed
mkdir assets
sudo chown -R www-data:www-data *

sudo apachectl restart

## with a browser:

Point your browser to the web server root.
Follow instructions for setting up HumHub.
DB hostname: localhost
DB username: sns
DB password: thisismypassword
Name of Database: sns

Sometimes it times out during the initial install process. If that happens, just let it go for 10 minutes and point to the base URL again. It should allow you to finish now. Apparently it takes a while to build the db, so the web server might not wait around long enough. When this happens, if you point back to the base URL it should pick up from where it left off.

Create and admin account when prompted.
I chose user: admin pw: thisismypassword

The install will fail at this step. This is normal because the code is trying to find some missing columns.

## go back to Linux command prompt

sudo mysql

## you will now be at the mysql prompt

USE sns;

ALTER TABLE content ADD COLUMN pol_op TINYINT(4) NULL DEFAULT '2' AFTER visibility;

ALTER TABLE user ADD COLUMN pol_op TINYINT(4) NULL DEFAULT '2' AFTER status;

quit

## you will now be at the Linux command prompt

## with a browser:
Return to the base URL and follow the steps. This time you will get to the Example content step. 
Uncheck the box to NOT create sample content. 

This completes the install and allows you to sign in as admin.

SUCCESS!

<<<<<<< End Instructions >>>>>>>


RewriteCond %{HTTP_HOST} ^partake.social
RewriteRule ^(.*) http://203.101.226.107/sns-experiment/$1 [P]
