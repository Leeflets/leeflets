<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase <?php echo $rewrite_base, "\n"; ?>
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
</IfModule>

# If we don't check for the PHP module
# we'll get 500 error on hosts running PHP as a CGI
<IfModule mod_php5.c>
# In case this is on in php.ini
php_flag magic_quotes_gpc Off

# Try bumping up these values if you're getting errors uploading large files
php_value post_max_size 50M
php_value upload_max_filesize 20M
</IfModule>
