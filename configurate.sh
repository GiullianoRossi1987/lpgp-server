#!/usr/bin/env bash

echo -e "WORKING ON THE CONFIGURATION"
echo -e "CONFIGURATION DATA RECEIVE PROCESS"
echo -e "First of all type the certificate (.crt) file path: "; read lcert
echo -e "Now the ceritificate key file (.key) path: "; read lkey
echo -e "Now type the document root of the LPGP installation: "; read ldr
echo -e "Done. Please wait"
echo -e "CONFIGURATION OF ADMIN"
echo -e "Ok, now tell me the server admin name: "; read adm
echo -e "We are just finishing.\nNow you want to configure the Apache installation automaticaly, or by your self? [1] just do it/ any other key to make it yourself"; read con
echo -e "Working ..."

#configuring local apache conf

echo -e "
<VirtualHost *:443>
    ServerName www.lpgpofficial.com
    ServerAdmin $adm
    SSLEngine On
    SSLCertificateFile $lcert
    SSLCertificateKeyFile $lkey
    DocumentRoot $ldr

    <Directory $ldr>
      Options Indexes FollowSymLinks ExecCGI
      Options +ExecCGI
      SetHandler cgi-script
      AddHandler cgi-script .php .pl
      Require all granted
      Order allow,deny
      Allow from all
    </Directory>

    <Directory $ldr/core>
      Options Indexes FollowSymLinks ExecCGI
      Options +ExecCGI
      SetHandler cgi-script
      AddHandler cgi-script .php .pl
      Require all granted
      Order allow,deny
      Allow from all
    </Directory>

    <Directory $ldr/cgi-actions>
      Options Indexes FollowSymLinks ExecCGI
      Options +ExecCGI
      SetHandler cgi-script
      AddHandler cgi-script .php .pl
      Require all granted
      Order allow,deny
      Allow from all
    </Directory>
</VirtualHost>
" | tee -a config/apache.conf

if [ $con = "1" ]
then
  touch /etc/apache2/sites-available/lpgp.conf || echo "ERROR: Please make sure you executed that script as a root user, "; exit 124
  echo -e "
  <VirtualHost *:443>
      ServerName www.lpgpofficial.com
      ServerAdmin $adm
      SSLEngine On
      SSLCertificateFile $lcert
      SSLCertificateKeyFile $lkey
      DocumentRoot $ldr

      <Directory $ldr>
        Options Indexes FollowSymLinks ExecCGI
        Options +ExecCGI
        SetHandler cgi-script
        AddHandler cgi-script .php .pl
        Require all granted
        Order allow,deny
        Allow from all
      </Directory>

      <Directory $ldr/core>
        Options Indexes FollowSymLinks ExecCGI
        Options +ExecCGI
        SetHandler cgi-script
        AddHandler cgi-script .php .pl
        Require all granted
        Order allow,deny
        Allow from all
      </Directory>

      <Directory $ldr/cgi-actions>
        Options Indexes FollowSymLinks ExecCGI
        Options +ExecCGI
        SetHandler cgi-script
        AddHandler cgi-script .php .pl
        Require all granted
        Order allow,deny
        Allow from all
      </Directory>
  </VirtualHost>
  " | tee -a /etc/apache2/sites-available/lpgp.conf || echo "ERROR: Please make sure you executed that script as a root user, "; exit 124
  service apache2 restart
fi
echo -e "Done\nLPGP ";
