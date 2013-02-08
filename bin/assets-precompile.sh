LESSPHP_PATH=../vendor/leafo/lessphp/plessc
COFFEE_PATH=./coffee.php
CLOSURE_PATH=./java/compiler.jar

domainsCount=1
domains=(domain)

for ((i=0; i<domainsCount; i++)); do

    # remove old assets
    rm -Rf ../public/${domains[$i]}/css/*
    rm -Rf ../public/${domains[$i]}/js/*
    rm -Rf ../public/${domains[$i]}/img/*

    # copy css, js, img
    cp -Rf ../assets/common/js/* ../public/${domains[$i]}/js/
    cp -Rf ../assets/common/css/* ../public/${domains[$i]}/css/
    cp -Rf ../assets/common/img/* ../public/${domains[$i]}/img/
    cp -Rf ../assets/custom/${domains[$i]}/js/* ../public/${domains[$i]}/js/
    cp -Rf ../assets/custom/${domains[$i]}/css/* ../public/${domains[$i]}/css/
    cp -Rf ../assets/custom/${domains[$i]}/img/* ../public/${domains[$i]}/img/

    # compile less
    php ${LESSPHP_PATH} -f=compressed ../assets/common/less/core.less ../public/${domains[$i]}/css/core.min.css
    php ${LESSPHP_PATH} -f=compressed ../assets/common/less/admin.less ../public/${domains[$i]}/css/admin.min.css

    if [ -f ../assets/custom/${domains[$i]}/less/custom.less ];
    then
        php ${LESSPHP_PATH} -f=compressed ../assets/custom/${domains[$i]}/less/custom.less ../public/${domains[$i]}/css/custom.min.css
    fi

    # compile coffee
    php ${COFFEE_PATH} -i ../assets/common/coffee/core.coffee -o ../public/${domains[$i]}/js/core.js
    if [ -f ../assets/custom/${domains[$i]}/coffee/custom.coffee ];
    then
        php ${COFFEE_PATH} -i ../assets/custom/${domains[$i]}/coffee/custom.coffee -o ../public/${domains[$i]}/js/custom.js
    fi

done

echo "Done."