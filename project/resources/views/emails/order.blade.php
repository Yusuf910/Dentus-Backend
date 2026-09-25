<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#eff2f7" align="center" style="border-collapse:collapse">
   <tbody>
      <tr>
         <td valign="top" align="center" height="30"></td>
      </tr>
      <tr>
         <td valign="top" align="center" width="600">
            <table border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;border-collapse:collapse;border:1px solid #f0f1f6">
               <tbody>
                  <tr>
                     <td width="600" valign="top" bgcolor="#FFFFFF" align="center" style="max-width:600px">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                           <tbody>
                              <tr>
                                 <td valign="top" align="center" bgcolor="#f7f8fa">
                                    <table width="95%" align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                                       <tbody>
                                          <tr>
                                             <td valign="top" height="18"></td>
                                          </tr>
                                          <tr>
                                             <td valign="top">
                                                <table width="105" align="left" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                                                   <tbody>
                                                      <tr>
                                                         <td height="29"><a href="#" style="font-size:14px;text-align:left;color:#006ab9;text-decoration:none" target="_blank"> <img src="{{ asset('assets/images') }}/logo-light-text2.png" width="150" alt="" border="0" style="font-family:'Roboto',Arial;font-size:14px;text-align:left;color:#006ab9" class="CToWUd"></a> </td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td valign="top" height="15"></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" align="center">
                                    <table width="92%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                                       <tbody>
                                          <tr>
                                             <td height="30"></td>
                                          </tr>
                                          <tr>
                                             <td valign="top" align="left">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                                                   <tbody>
                                                      <tr>
                                                         <td align="left" style="font-family:'Roboto',Arial;text-align:left;font-size:14px;color:#666666;line-height:20px">
                                                            Hello {{$data['name']}},
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td height="20"></td>
                                                      </tr>
                                                      <tr>
                                                         <td align="left" style="font-family:'Roboto',Arial;text-align:left;font-size:14px;color:#666666;line-height:20px">Your order added successfully:</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td height="20"></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" align="center">
                                    <table width="92%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                                       <tbody>
                                          <tr>
                                             <td height="30"></td>
                                          </tr>
                                          <td valign="top" align="center">
                                             <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                                                <tbody>
                                                   <tr>
                                                      <td valign="top" align="left">
                                                         <table style="width:100%; border-collapse: collapse; border: 1px solid #000;" border="1">
                                                            <tr align="left" style="background-color: #333; color: #fff;">
                                                               <th style="font-size: 14px; text-align: center;">#</th>
                                                               <th style="font-size: 14px; text-align: center;">Product Info</th>
                                                               <th style="font-size: 14px; text-align: center;">Price</th>
                                                               <th style="font-size: 14px; text-align: center;">Qty</th>
                                                               <th style="font-size: 14px; text-align: center;">Total</th>
                                                            </tr>
                                                            <?php $total_amount = 0;
                                                            	  $discountamount = 0;
                                                             if (count($data['content']) > 0): $i=1;?>
                                                            @foreach($data['content'] as $a)
                                                            <?php $bb = App\Models\Book::find($a->book_id); ?>
                                                            @if($bb)
                                                            <?php $total_amount += $a->amount;
                                                            $discountamount += $a->discount_amount; ?>
                                                            	<tr>
	                                                               <td>
	                                                                  <strong>{{$i}}</strong>
	                                                               </td>
	                                                               <td style="font-size:16px;">{{mb_strlen($bb->name,'utf-8') > 45 ? mb_substr($bb->name,0,45,'utf-8').'...' : $bb->name}}</td>
	                                                               <td style="text-align: center;">₹ {{$a->amount}}</td>
	                                                               <td style="text-align: center;">1</td>
	                                                               <td style="text-align: center;">₹ {{$a->amount}}</td>
	                                                            </tr>
	                                                        <?php $i++; ?>
	                                                        
	                                                        @endif
	                                                        @endforeach
                                                            <?php endif ?>
                                                         </table>
                                                      </td>
                                                   </tr>
                                                   <tr>
                                                      <td height="20"></td>
                                                   </tr>
                                                </tbody>
                                             </table>
                                             <?php if ($total_amount > 0): ?>
                                             <table style="width: 50%;" align="right">
                                                <tr>
                                                   <td style="text-align: right;">Total</td>
                                                   <td style="text-align: right;">₹ {{$total_amount}}</td>
                                                </tr>
                                                <?php if ($discountamount > 0): ?>
                                                <tr>
                                                   <td style="text-align: right;">Coupan Discount</td>
                                                   <td style="text-align: right; color: #f00;">₹ {{$discountamount}}</td>
                                                </tr>
                                                <?php endif ?>
                                                <tr>
                                                   <td style="text-align: right;"><strong>Payable Amount</strong></td>
                                                   <td style="text-align: right;"><strong>₹{{$total_amount-$discountamount}}</strong></td>
                                                </tr>
                                             </table>
                                             <?php endif ?>
                                             
                                          </td>
                                          <tr>
                                             <td height="20"></td>
                                          </tr>
                                          <tr>
                                             <td height="20"></td>
                                          </tr>
                                          <tr>
                                             <td align="left" style="font-family:'Roboto',Arial;text-align:left;font-size:14px;color:#666666;line-height:20px">Regards<br>Pegasus Support Team</td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
      <tr>
         <td valign="top" align="center">
            <table style="width:100%;max-width:100%;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
               <tbody>
                  <tr>
                     <td align="center">
                        <!--container-->
                        <table class="row" style="width:600px;max-width:600px;" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                           <tbody>
                              <tr>
                                 <td bgcolor="#050B19" align="center">
                                    <!--wrapper-->
                                    <table class="row" style="width:540px;max-width:540px;" width="540" cellspacing="0" cellpadding="0" border="0" align="center">
                                       <tbody>
                                          <tr>
                                             <td class="container-padding" align="center">
                                                <!-- content container -->
                                                <table width="540" border="0" cellpadding="0" cellspacing="0" align="center" class="row" style="width:540px;max-width:540px;">
                                                   <tbody>
                                                      <tr>
                                                         <td align="center">
                                                            <!-- content -->    
                                                            <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center" style="width:100%; max-width:100%;">
                                                               <tbody>
                                                                  <tr>
                                                                     <td height="40">&nbsp;</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td align="center" style="font-family:'Josefin Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #dadada;font-weight: 400;">Get in Touch</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td height="20">&nbsp;</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td>
                                                                        <table cellspacing="0" cellpadding="0" border="0" align="center">
                                                                           <tbody>
                                                                              <tr>
                                                                                 <td width="25">
                                                                                    <a href="{{$data['generalsettings']->fb_link}}"><img width="25" style="display:block;width:100%;max-width:25px;" src="{{ asset('assets/emailtemplates') }}/fb.png">
                                                                                    </a>
                                                                                 </td>
                                                                                 <td width="10">&nbsp;</td>
                                                                                 <td width="25">
                                                                                    <a href="{{$data['generalsettings']->twitter_link}}"><img width="25" style="display:block;width:100%;max-width:25px;" src="{{ asset('assets/emailtemplates') }}/tw.png">
                                                                                    </a>
                                                                                 </td>
                                                                                 <td width="10">&nbsp;</td>
                                                                                 <td width="25">
                                                                                    <a href="{{$data['generalsettings']->linkedin_link}}"><img width="25" style="display:block;width:100%;max-width:25px;" src="{{ asset('assets/emailtemplates') }}/in.png"></a>
                                                                                 </td>
                                                                              </tr>
                                                                           </tbody>
                                                                        </table>
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td height="20">&nbsp;</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 19px">
                                                                        Kindly note that this is a system generated email. PLEASE DO NOT REPLY.<br> For any help assistance and support, you can email us on <a href="mailto:{{$data['generalsettings']->support_email}}" target="_blank" style="color: #dadada">{{$data['generalsettings']->support_email}}.</a>
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td>&nbsp;</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td align="center">
                                                                        <table cellspacing="0" cellpadding="0" border="0">
                                                                           <tbody>
                                                                              <tr>
                                                                                 <td align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 20px;text-decoration: underline"><a href="{{url('/term')}}" target="_blank" style="color: #dadada">Terms of service</a></td>
                                                                                 <td width="20" align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 20px;">|</td>
                                                                                 <td align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 20px;text-decoration: underline"><a href="{{url('/privacy')}}" target="_blank" style="color: #dadada">Privacy Policy</a></td>
                                                                              </tr>
                                                                           </tbody>
                                                                        </table>
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td height="40">&nbsp;</td>
                                                                  </tr>
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>