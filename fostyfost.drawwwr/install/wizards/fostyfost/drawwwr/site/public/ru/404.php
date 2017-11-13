<?php
/**
 * @global \CMain $APPLICATION
 * @bxnolanginspection
 */

include_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/urlrewrite.php');

\CHTTP::SetStatus('404 Not Found');

@define('ERROR_404', 'Y');

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/header.php');

$APPLICATION->SetTitle('Страница не найдена');
?>
    <style>
        @import url(https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css);
        @import url(https://fonts.googleapis.com/css?family=Open+Sans:300|Titillium+Web:700);

        body {
            background: url(http://i.giphy.com/jcxtvm2bsZDH2.gif);
            background-repeat: no-repeat;
            background-position: bottom center;
            background-size: 90%, cover;
            font-family: 'Titillium Web', sans-serif;
            color: #fff;
            background-color: #1BCF00;
        }

        .travolta {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.65);
            min-height: 100vh;
            text-align: center;
        }

        .travolta h1 {
            font-size: 30vw;
            font-weight: 700;
        }

        .travolta p {
            font-family: 'Open Sans', sans-serif;
            font-weight: 300;
            font-size: 5vw;
            margin-bottom: 30px;
        }

        .travolta a {
            border-radius: 25px;
            background: none;
            color: #fff;
            font-size: 18px;
            padding: 10px 20px;
            border: 2px solid #fff;
            text-decoration: none;
        }

        .travolta a:hover {
            background: #fff;
            color: #212121;
        }
    </style>
    <div class="travolta">
        <h1>404</h1>
        <p>страница не найдена</p>
        <a href="/">перейти на главную</a>
    </div>
<?php
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/footer.php');
