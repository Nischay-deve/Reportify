<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
<style>
    @import url("https://fonts.googleapis.com/css2?family=Baloo+2&amp;display=swap");

    @media (min-width:320px)  { /* smartphones, iPhone, portrait 480x320 phones */ }
    @media (min-width:481px)  { /* portrait e-readers (Nook/Kindle), smaller tablets @ 600 or @ 640 wide. */ }
    @media (min-width:641px)  { /* portrait tablets, portrait iPad, landscape e-readers, landscape 800x480 or 854x480 phones */ }
    @media (min-width:961px)  { /* tablet, landscape iPad, lo-res laptops ands desktops */ }
    @media (min-width:1025px) { /* big landscape tablets, laptops, and desktops */ }
    @media (min-width:1281px) { /* hi-res laptops and desktops */ }   
  
    .swiper-button-next,
    .swiper-button-prev {
        top: 320px;
        cursor: pointer;
        background-color: #ffffff;
        box-shadow: 0 7px 12px -7px rgba(164, 176, 199);
        border-radius: 10px;
        width: 50px;
        height: 50px;
        color: #22294f;
        background-image: none;
    }

    .swiper-pagination-fraction {
        font-size: 13px;
        font-weight: bold;
        color: #22294f;
    }

    .swiper-pagination-current,
    .swiper-pagination-total {
        font-size: 13px;
        font-weight: bold;
        color: #22294f;
    }


    .zoom-image-control {
        position: absolute;
        background-color: rgba(0, 0, 0, 0.8);
        border-radius: 4px;
        right: 15px;
        top: 15%;
        margin-top: -48px;
        z-index: 1000;
    }

    .zoom-image-in {
        background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgc3R5bGU9IiI+PHJlY3QgaWQ9ImJhY2tncm91bmRyZWN0IiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4PSIwIiB5PSIwIiBmaWxsPSJub25lIiBzdHJva2U9Im5vbmUiLz48ZyBjbGFzcz0iY3VycmVudExheWVyIiBzdHlsZT0iIj48dGl0bGU+TGF5ZXIgMTwvdGl0bGU+PHBhdGggZD0iTTE5IDEzaC02djZoLTJ2LTZINXYtMmg2VjVoMnY2aDZ2MnoiIGlkPSJzdmdfMSIgY2xhc3M9IiIgc3Ryb2tlPSJub25lIiBmaWxsPSIjZmZmZmZmIiBmaWxsLW9wYWNpdHk9IjEiLz48cGF0aCBkPSJNLTE1LjgzNjczNDQyMDQ2MTY1Myw0NC41MzU0MDkzMDY3MTAxOCBoNTguMjA0MDgwODI3NTkzMDkgdi02LjU3NjIyNjcyMzM2OTIyMTUgSC0xNS44MzY3MzQ0MjA0NjE2NTMgeiIgZmlsbD0ibm9uZSIgaWQ9InN2Z18yIiBjbGFzcz0iIiBzdHJva2U9Im5vbmUiLz48L2c+PC9zdmc+);
    }

    .zoom-image-out {
        background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCI+PHJlY3QgaWQ9ImJhY2tncm91bmRyZWN0IiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4PSIwIiB5PSIwIiBmaWxsPSJub25lIiBzdHJva2U9Im5vbmUiLz48ZyBjbGFzcz0iY3VycmVudExheWVyIiBzdHlsZT0iIj48dGl0bGU+TGF5ZXIgMTwvdGl0bGU+PHBhdGggZD0iTTE5IDEzSDV2LTJoMTR2MnoiIGlkPSJzdmdfMSIgY2xhc3M9IiIgZmlsbD0iI2ZmZmZmZiIgZmlsbC1vcGFjaXR5PSIxIi8+PC9nPjwvc3ZnPg==);
    }

    .zoom-image-in,
    .zoom-image-out {
        z-index: 1050;
        width: 48px;
        height: 48px;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 1;
        cursor: pointer;
    }

    .zoom-image-in:focus,
    .zoom-image-out:focus {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .mr-2 {
        margin-right: 2px;
    }

    .fsicon {
        font-size: 15px;
    }

    .dark {
        background: #110f16;
    }

    .light {
        background: #f3f5f7;
    }

    a,
    a:hover {
        text-decoration: none;
        transition: color 0.3s ease-in-out;
    }

    #pageHeaderTitle {
        margin: 2rem 0;
        text-align: center;
        font-size: 25px;
    }

    /* Cards */
    .postcard {
        flex-wrap: wrap;
        display: flex;
        box-shadow: 0 4px 21px -12px rgba(0, 0, 0, 0.66);
        border-radius: 10px;
        margin: 0 0 2rem 0;
        overflow: hidden;
        position: relative;
        color: #ffffff;
        text-align: left;
    }

    .postcard.dark {
        background-color: #18151f;
    }

    .postcard.light {
        background-color: #e1e5ea;
    }

    .postcard .t-dark {
        color: #18151f;
    }

    .postcard a {
        color: inherit;
    }

    .postcard h1,
    .postcard .h1 {
        margin-bottom: 0.5rem;
        font-weight: 500;
        line-height: 1.2;
    }

    .postcard .small {
        font-size: 80%;
    }

    .postcard .postcard__title {
        font-size: 20px;
        text-align: left;
    }

    .postcard .postcard__img {
        width: 100%;
        object-fit: contain;
        position: relative;
    }

    .downloadImage {
        padding: 6px;
        border-radius: 5px;
        background-color: white;
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;        

        box-shadow: inset 0 0 0 0 #dadada;
        color: #dadada;
        margin: 0 -.25rem;
        padding: 0 .25rem;
        transition: color .3s ease-in-out, box-shadow .3s ease-in-out;        
    }

    .downloadImage:hover{
        box-shadow: inset 100px 0 0 0 #dadada;
    } 

    .downloadImage i{
        color: black;
    }

    

    .postcard__subtitle {
        font-size: 15px;
    }

    .postcard .postcard__img_link {
        display: contents;
    }

    .postcard .postcard__bar {
        width: 50px;
        height: 10px;
        margin: 10px 0;
        border-radius: 5px;
        background-color: #424242;
        transition: width 0.2s ease;
    }

    .postcard .postcard__text {
        padding: 1rem 2rem;
        position: relative;
        display: flex;
        flex-direction: column;
        text-align: left;
    }

    .postcard .postcard__preview-txt ol {
        margin-right: 15px;
    }

    .postcard .postcard__preview-txt {
        /* overflow: hidden; */
        overflow-y: auto;
        text-overflow: ellipsis;
        text-align: justify;
        /* height: 400px; */
        margin-right: 15px;
        /* font-size: 20px; */
        font-size: 16px;

        /* -ms-overflow-style: none;
        scrollbar-width: none;  */
    }

    .postcard .postcard__preview-txt::-webkit-scrollbar {
        /* display: none; */
    }


    .postcard .postcard__preview-txt::-webkit-scrollbar {
        width: 12px;
    }

    /* Track */
    .postcard .postcard__preview-txt::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
        -webkit-border-radius: 10px;
        border-radius: 10px;
    }

    /* Handle */
    .postcard .postcard__preview-txt::-webkit-scrollbar-thumb {
        -webkit-border-radius: 10px;
        border-radius: 10px;
        background: #dadada; 
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
    }
    .postcard .postcard__preview-txt::-webkit-scrollbar-thumb:window-inactive {
        background: rgba(83, 83, 83, 0.4);
    }    
    

    .postcard .postcard__tagbox {
        display: flex;
        flex-flow: row wrap;
        font-size: 14px;
        margin: 20px 0 0 0;
        padding: 0;
        justify-content: center;
    }

    .postcard .postcard__tagbox .tag__item {
        display: inline-block;
        background: rgba(83, 83, 83, 0.4);
        border-radius: 3px;
        padding: 2.5px 10px;
        margin: 0 5px 5px 0;
        cursor: default;
        user-select: none;
        transition: background-color 0.3s;
    }

    .postcard .postcard__tagbox .tag__item:hover {
        background: rgba(83, 83, 83, 0.8);
        color: #ffffff;
    }

    .postcard:before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: linear-gradient(-70deg, #424242, transparent 50%);
        opacity: 1;
        border-radius: 10px;
    }

    .postcard:hover .postcard__bar {
        width: 100px;
    }

    @media only screen and (min-width: 769px) {
        .postcard {
            flex-wrap: inherit;
        }

        .postcard .postcard__title {
            font-size: 20px;
        }

        .postcard .postcard__tagbox {
            justify-content: start;
        }

        .postcard .postcard__img {
            /* max-width: 500px; */
            max-width: 100%;
            max-height: 100%;
            transition: transform 0.3s ease;
        }

        .postcard .postcard__text {
            padding: 1rem 2rem;
            width: 100%;
        }

        .postcard .media.postcard__text:before {
            content: "";
            position: absolute;
            display: block;
            background: #18151f;
            top: -20%;
            height: 130%;
            width: 55px;
        }

        .postcard:hover .postcard__img {
            transform: scale(1.1);
        }
    }

    @media screen and (min-width: 1024px) {
        .postcard__text {
            padding: 2rem 3.5rem;
        }

        .postcard.dark .postcard__text:before {
            background: #18151f;
        }

        .postcard.light .postcard__text:before {
            background: #e1e5ea;
        }
    }

    /* COLORS */
    .postcard .postcard__tagbox .green.play:hover {
        background: #79dd09;
        color: black;
    }

    .green .postcard__title:hover {
        color: #79dd09;
    }

    .green .postcard__bar {
        background-color: #79dd09;
    }

    .green::before {
        background-image: linear-gradient(-30deg, rgba(121, 221, 9, 0.1), transparent 50%);
    }

    .green:nth-child(2n)::before {
        background-image: linear-gradient(30deg, rgba(121, 221, 9, 0.1), transparent 50%);
    }

    .postcard .postcard__tagbox .blue.play:hover {
        background: #0076bd;
    }

    .blue .postcard__title:hover {
        color: #0076bd;
    }

    .blue .postcard__bar {
        background-color: #0076bd;
    }

    .blue::before {
        background-image: linear-gradient(-30deg, rgba(0, 118, 189, 0.1), transparent 50%);
    }

    .blue:nth-child(2n)::before {
        background-image: linear-gradient(30deg, rgba(0, 118, 189, 0.1), transparent 50%);
    }

    .postcard .postcard__tagbox .red.play:hover {
        background: #bd150b;
    }

    .red .postcard__title:hover {
        color: #bd150b;
    }

    .red .postcard__bar {
        background-color: #bd150b;
    }

    .red::before {
        background-image: linear-gradient(-30deg, rgba(189, 21, 11, 0.1), transparent 50%);
    }

    .red:nth-child(2n)::before {
        background-image: linear-gradient(30deg, rgba(189, 21, 11, 0.1), transparent 50%);
    }

    .postcard .postcard__tagbox .yellow.play:hover {
        background: #bdbb49;
        color: black;
    }

    .yellow .postcard__title:hover {
        color: #bdbb49;
    }

    .yellow .postcard__bar {
        background-color: #bdbb49;
    }

    .yellow::before {
        background-image: linear-gradient(-30deg, rgba(189, 187, 73, 0.1), transparent 50%);
    }

    .yellow:nth-child(2n)::before {
        background-image: linear-gradient(30deg, rgba(189, 187, 73, 0.1), transparent 50%);
    }

    @media screen and (min-width: 769px) {
        .green::before {
            background-image: linear-gradient(-80deg, rgba(121, 221, 9, 0.1), transparent 50%);
        }

        .green:nth-child(2n)::before {
            background-image: linear-gradient(80deg, rgba(121, 221, 9, 0.1), transparent 50%);
        }

        .blue::before {
            background-image: linear-gradient(-80deg, rgba(0, 118, 189, 0.1), transparent 50%);
        }

        .blue:nth-child(2n)::before {
            background-image: linear-gradient(80deg, rgba(0, 118, 189, 0.1), transparent 50%);
        }

        .red::before {
            background-image: linear-gradient(-80deg, rgba(189, 21, 11, 0.1), transparent 50%);
        }

        .red:nth-child(2n)::before {
            background-image: linear-gradient(80deg, rgba(189, 21, 11, 0.1), transparent 50%);
        }

        .yellow::before {
            background-image: linear-gradient(-80deg, rgba(189, 187, 73, 0.1), transparent 50%);
        }

        .yellow:nth-child(2n)::before {
            background-image: linear-gradient(80deg, rgba(189, 187, 73, 0.1), transparent 50%);
        }
    }
 
    #incidents{
    text-align: center;
    font-weight: bold;
    font-size: 26px;
    }

    .max-image-height-desktop {
        max-height: 500px !important;
    }

    .max-image-height-mobile {
        max-height: 500px !important;
    }

    .storyDetailText{
        /* font-size: 22px; */
        font-size: 16px;
        font-weight: normal;
        color: black;    
    }

    .postcard__bar_blue {
        background-color: #0076bd !important;
    }

    .postcard__bar_red {
        background-color: #bd150b !important;
    }
</style>
