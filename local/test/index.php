<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        BX.ajax.runAction('zmkhitarian:testmodule.api.Reviews.getList', {
            data: {
                page: 2,
                limit: 1
            }
        }).then((response) => {
                console.log(response.data);
            },
            (response) => {
                console.error(response.errors);
            });
    })
</script>
