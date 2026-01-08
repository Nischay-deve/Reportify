<!DOCTYPE html>
<html>
<head>


    <style>
        body {
            width: 100%;
            font-family: 'Hind', serif;
        }

        /*Common CSS Code for Table*/
        .tableStyleHeading {
            width: 100%;

        }

        .tableStyle {
            width: 100%;
            /* line-height: 1.50; */
        }

        /*TD and TH Style*/
        .tableStyle td,
        .tableStyle th {
            border: 1px solid #000000;
            padding: 10px;
        }

        /*Style for Table Head - TH*/
        .tableStyle th {
            border: solid #000;
            border-width: 0 1px;
            text-align: center;
        }

        .tableStyle caption {
            text-align: center;
        }

        ul {
            list-style-type: none;
            /* Remove bullets */
            padding: 0;
            /* Remove padding */
            margin: 0;
            /* Remove margins */
        }
        a{
            color:blue;
        }
    </style>
</head>

<body>
    <table align="left" cellspacing="0" cellpadding="0" style="width:100%;text-align:left;">
        <tbody>
            ##CONTENT##
        </tbody>
    </table>
</body>
</html>
