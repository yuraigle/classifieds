php doctrine.php orm:clear-cache:metadata
# php doctrine.php orm:generate-entities --regenerate-entities=* ../doctrine
php doctrine.php orm:schema-tool:update --force
php doctrine.php orm:generate-proxies