LESSPHP_PATH=../vendor/leafo/lessphp/plessc
COFFEE_PATH=./coffee.php

domainsCount=1
domains=(domain)

for ((i=0; i<domainsCount; i++)); do

    PUBLIC_PATH=../public/${domains[$i]}

    # create assets dir if not exists
    mkdir -p ${PUBLIC_PATH}/css
    mkdir -p ${PUBLIC_PATH}/js
    mkdir -p ${PUBLIC_PATH}/img
    mkdir -p ${PUBLIC_PATH}/fonts

    # remove old assets
    rm -Rf ${PUBLIC_PATH}/css/*
    rm -Rf ${PUBLIC_PATH}/js/*
    rm -Rf ${PUBLIC_PATH}/img/*
    rm -Rf ${PUBLIC_PATH}/fonts/*

    # copy css, js, img
    cp -Rf ../assets/common/js/* ${PUBLIC_PATH}/js/
    cp -Rf ../assets/common/css/* ${PUBLIC_PATH}/css/
    cp -Rf ../assets/common/img/* ${PUBLIC_PATH}/img/
    cp -Rf ../assets/common/fonts/* ${PUBLIC_PATH}/fonts/
    cp -Rf ../assets/custom/${domains[$i]}/js/* ${PUBLIC_PATH}/js/
    cp -Rf ../assets/custom/${domains[$i]}/css/* ${PUBLIC_PATH}/css/
    cp -Rf ../assets/custom/${domains[$i]}/img/* ${PUBLIC_PATH}/img/
    cp -Rf ../assets/custom/${domains[$i]}/fonts/* ${PUBLIC_PATH}/fonts/

    # compile less
    php ${LESSPHP_PATH} -f=compressed ../assets/common/less/core.less ${PUBLIC_PATH}/css/core.min.css
    php ${LESSPHP_PATH} -f=compressed ../assets/common/less/admin.less ${PUBLIC_PATH}/css/admin.min.css

    if [ -f ../assets/custom/${domains[$i]}/less/custom.less ];
    then
        php ${LESSPHP_PATH} -f=compressed ../assets/custom/${domains[$i]}/less/custom.less ${PUBLIC_PATH}/css/custom.min.css
    fi

    # compile coffee
    php ${COFFEE_PATH} -i ../assets/common/coffee/core.coffee -o ${PUBLIC_PATH}/js/core.js
    if [ -f ../assets/custom/${domains[$i]}/coffee/custom.coffee ];
    then
        php ${COFFEE_PATH} -i ../assets/custom/${domains[$i]}/coffee/custom.coffee -o ${PUBLIC_PATH}/js/custom.js
    fi
done

echo "Done."