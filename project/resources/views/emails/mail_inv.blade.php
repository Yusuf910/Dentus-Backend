<center>
    <table style="background-color:#1cae81;width:640px;">
        <tbody>
            <tr>
                <td>
                    <center>
                        <table style="background-color:white;width:600px;margin-top:5%;margin-bottom:5%">
                            <tbody>
                                <tr>
                                    <td>
                                        <a href="#" target="_blank"><img src="{{asset('content/')}}/logo.png" style="max-width:100%;margin-top:16px;width:30%;margin-bottom:3px;margin-left:5%"></a>
                                        <hr style="border:0px;border-bottom:1px solid #eee">
                                    </td>
                                </tr>
                                <tr style="margin:0px;margin-top:3%;padding:0px">
                                    <td style="padding-left:5%;padding-right:5%;vertical-align:top;margin-bottom:0px;padding-bottom:0px">

                                        <p style="font-weight:bold;font-size:20px; font-family:helvetica;">Hi {{$invitationData['name']}} {{$invitationData['last_name']}},</p>
                                        <!-- <p style="font-size:18px; font-family:helvetica;">Hi shahnawaz</p> -->
                                        <p style="font-size:18px; font-family:helvetica;">Welcome to Dentus App</p>
                                        <p style="font-size:16px; font-family:helvetica; line-height: 25px;">I am excited to invite you to join Dentus, our new dental practice management app that will help streamline our daily operations and improve communication within the team.</p>

                                            <p style="font-size:16px; font-family:helvetica; line-height: 25px;">With Dentus, you'll be able to:</p>
                                            <p style="font-size:16px; font-family:helvetica; line-height: 25px;">
                                            Manage appointments and assist with scheduling.
                                            Communicate effectively with patients and the rest of the team.
                                            Access patient information securely and efficiently.</p>
                                            <p style="font-size:16px; font-family:helvetica; line-height: 25px;">
                                            How to Get Started:</p>
                                            <p style="font-size:16px; font-family:helvetica; line-height: 25px;">
                                            Download the Dentus app from the [App Store Link] or [Google Play Link].
                                            Click on this invitation link to sign up: [Signup Link].
                                            <p style="font-size:16px; font-family:helvetica; line-height: 25px;">
                                            Complete your profile, and you'll be ready to assist in managing our practice through the app.
                                            If you have any questions or need help signing up, feel free to reach out to me or contact Dentus support at [Support Email].
                                            <p style="font-size:16px; font-family:helvetica; line-height: 25px;">
                                            I look forward to having you on board as we make our workflow more efficient with Dentus!
                                            <p style="font-size:16px; font-family:helvetica; line-height: 25px;">
                                            Best regards,
                                            </p>
                                            {{$doctor['name']}} {{$doctor['last_name']}}<br>
                                            {{$doctor['mobile']}}<br>
                                            </p>
                                        </p>

                                      
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td style="width:250px;font-size:16px;color:#777;font-family:helvetica;">
                                                        Thank You

                                                    </td>
                                                </tr>
                                                <tr height="20"></tr>
                                            </tbody>
                                        </table>

                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </center>

                </td>
            </tr>
            <tr height="5"></tr>
            <tr>
                <td>

                    <center>
                        <p style="font-size:12px;font-family:helvetica;margin-left:3%;margin-top:0px;color: #fff;">Need Help?
                                Call us <a href="tel:{{$s->helpline_number}}" style="color:#fff;">{{$s->helpline_number}}</a>&nbsp;-&nbsp;<a href="tel:{{$s->helpline_number}}" style="color:#fff;">{{$s->helpline_number}}</a></p>
                        <p style="font-size:12px;color:#fff;font-family:helvetica;">© 2024 Dentus App. All rights reserved.</p>
                    </center>
                </td>
            </tr>
            <tr height="20"></tr>
        </tbody>
    </table>
</center>