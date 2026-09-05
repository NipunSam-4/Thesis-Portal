<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Thesis Submission Certificate - {{ $rollNumber }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 22mm 24mm 20mm 24mm;
        }

        body {
            font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;
            color: #000000;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header-container {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }

        .header-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        .ref-date-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 28px;
        }

        .title-box {
            text-align: center;
            margin-bottom: 28px;
        }

        .title-text {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000000;
        }

        .body-p {
            text-align: justify;
            text-justify: inter-word;
            font-size: 12pt;
            line-height: 1.65;
            margin-bottom: 20px;
            color: #000000;
        }

        .body-p strong {
            font-weight: bold;
            color: #000000;
        }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }

        .sign-cell {
            width: 48%;
            text-align: center;
            vertical-align: top;
        }

        .sign-title {
            font-size: 12pt;
            font-weight: bold;
            line-height: 1.25;
            color: #000000;
        }
    </style>
</head>
<body>

    <!-- Official High-Resolution Header Banner (Contains bilingual emblem, text & rule) -->
    <div class="header-container">
        @if(!empty($headerBase64))
            <img src="{{ $headerBase64 }}" alt="Indian Institute of Technology Indore" />
        @endif
    </div>

    <!-- Reference & Issue Date -->
    <table class="ref-date-table">
        <tr>
            <td style="text-align: left; width: 50%;">
                IITI/Acad/Ph.D./{{ $rollNumber }}
            </td>
            <td style="text-align: right; width: 50%;">
                {{ $issueDate }}
            </td>
        </tr>
    </table>

    <!-- Subject Title -->
    <div class="title-box">
        <span class="title-text">TO WHOMSOEVER IT MAY CONCERN</span>
    </div>

    <!-- Body Paragraphs -->
    <div class="body-p">
        This is to certify that <strong>{{ $studentName }}</strong>, Roll Number <strong>{{ $rollNumber }}</strong>, was a Ph.D. student in the Department of <strong>{{ $departmentName }}</strong>, has submitted a thesis entitled <strong>“<em>{{ $thesisTitle }}</em>”</strong> in partial fulfillment of the requirements of the Ph.D. degree of this Institute on <strong>{{ $submissionDate }}</strong>.
    </div>

    <div class="body-p">
        The award of Ph.D. degree will be considered only on approval of the thesis by examiners and on satisfactory defense of the thesis at the Viva voce.
    </div>

    <div class="body-p">
        This certificate is issued based upon his/her request dated <strong>{{ $issueDate }}</strong>.
    </div>

    <!-- Official Signature and Seal Block -->
    <table class="sign-table">
        <tr>
            <td style="width: 52%;"></td>
            <td class="sign-cell">
                <div class="sign-title">
                    Deputy Registrar<br>
                    (Academic Affairs)
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

