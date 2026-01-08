<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            width: 100%;
            font-family: 'freeserif', sans-serif;
        }

        /*Common CSS Code for Table*/
        .tableStyleHeading {
            width: 100%;

        }

        .tableStyle {
            width: 100%;
            line-height: 1.50;
        }

        /*TD and TH Style*/
        .tableStyle td,
        .tableStyle th {
            /* border: 1px solid #000000; */
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

        a {
            color: blue;
        }
    </style>
</head>

<body>

    <table class="tableStyleHeading" cellspacing="0" cellpadding="2" style="text-align: center;">
        <tbody>
            <tr>
                <td style="text-align: center;font-size: 15px;font-weight:bold">SM Monitoring
                    Report</td>
            </tr>
        </tbody>
    </table>

    <table class="tableStyle" border="0" cellspacing="0" cellpadding="2" width="100%" >
        <tbody>
            @foreach ($all_items_sm_cal as $key => $item)
                @php
                    $dateTime = DateTime::createFromFormat('Y-m-d', $item->date);
                    $loadingForYear = $dateTime->format('Y');
                    // $displayItemDate = $dateTime->format('jS \o\f F Y');
                    $displayItemDate = $dateTime->format('M d, Y');
                @endphp

                <tr>
                    <td style=" text-align: center;">
                        @if ($item->image)
                            @php
                                $hashtag = $item->heading;
                                $finalScreenshot = Helper::getImageUrl($item->image);
                            @endphp

                            @if (!empty($item->link))
                                <a href="{{ $item->link }}" target="_blank" class="img-responsive">
                                    <img class="card-img-top" src="{{ $finalScreenshot }}" alt="{{ $item->hashtag }}" />
                                </a>
                            @else
                                <img class="card-img-top" src="{{ $finalScreenshot }}" alt="{{ $item->hashtag }}" />
                            @endif
                        @else
                            @if ($item->team_id)
                                @if (file_exists(URL::to('public/images/teams/' . $item->team_id . '.jpg')))
                                    <img class="card-img-top"
                                        src="{{ URL::to('public/images/teams/' . $item->team_id . '.jpg') }}" />
                                @else
                                    <img class="card-img-top" src="{{ URL::to('public/images/teams/0.jpg') }}" />
                                @endif
                            @else
                                <img class="card-img-top" src="{{ URL::to('public/images/teams/0.jpg') }}" />
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        <h5 class="card-title">
                            @if (!empty($item->link))
                                <a href="{{ $item->link }}" target="_blank"
                                    class="importo">{{ ucfirst($item->hashtag) }}</a>
                            @else
                                {{ ucfirst($item->hashtag) }}
                            @endif
                        </h5>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="card-text">
                            {!! $item->description !!}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a class="btn btn-link fs-5 fw-bold" href="{{ $item->link }}" target="_blank"
                            data-ripple-color="hsl(0, 0%, 67%)">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span class="d-none d-md-inline-block">
                                {{ $displayItemDate }}
                            </span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <hr>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

</body>

</html>
