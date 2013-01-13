LESSPHP_PATH=../vendor/leafo/lessphp/plessc
CLOSURE_PATH=./java/compiler.jar

# remove old assets
rm -Rf ../public/css/*
rm -Rf ../public/js/*
rm -Rf ../public/img/*

# copy external js
cp -Rf ../externals/js/* ../public/js/
# copy external css
cp -Rf ../externals/css/* ../public/css/
# copy external img
cp -Rf ../externals/img/* ../public/img/

# compile less, move to public
php ${LESSPHP_PATH} -f=compressed ../assets/less/core.less ../public/css/core.min.css
# compile coffee, move to public
php ./coffee.php
# compress scripts, move to public
java -jar ${CLOSURE_PATH} --js \
    ../public/js/core.js \
    --js_output_file=../public/js/core.min.js
# images
cp -Rf ../assets/img/* ../public/img

# permissions set
chmod -R 777 ../public/js/
chmod -R 777 ../public/css/
chmod -R 777 ../public/img/

echo "Done."