<?php
declare(strict_types=1);

namespace UCRM\HTTP\UI;

final class Header
{

    public static function renderHeader()
    {
        echo '
        <style>
            .header {
                padding: 1.07143rem;
                line-height: 1.78571rem;
                box-sizing: border-box;
                display: block;
                color: #000;
                font-family: "Lato","Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif;
                font-weight: 400;
                background-color: #edf0f3;
                overflow-x: hidden;
                -webkit-font-smoothing: antialiased;
                font-size: 14px;
            }
            
        
        </style>

        <div class="header">
            Test
        </div>
        ';


    }


    public static function renderTitle()
    {
        echo '
        

        <div style="page-header__top">
            <div class="page-header__left">
                Test
            </div>
            <div class="page-header__right">
            </div>
        </div>
        ';
    }







}