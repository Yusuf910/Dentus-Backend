 <?php
      $address1 = App\Models\OrderAddress::where('order_id',$order['id'])->where('address_type',1)->first();
      $address2 = App\Models\OrderAddress::where('order_id',$order['id'])->where('address_type',1)->first();
?>
<table style="width:100%;max-width:100%;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
   <tbody>
      <tr>
         <td bgcolor="#F4F4F4" align="center">
            <!--container-->
            <table class="row" style="width:600px;max-width:600px;" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
               <tbody>
                  <tr>
                     <td bgcolor="#0052cc" align="center">
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
                                                         <td height="25">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td>
                                                            <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center">
                                                               <tbody>
                                                                  <tr>
                                                                     <td width="120" align="left">
                                                                        <img width="120" style="display:block;width:100%;max-width:120px;" alt="img" src="{{ asset('assets/images') }}/gb-logo.png">
                                                                     </td>
                                                                  </tr>
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td height="25">&nbsp;</td>
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
<table style="width:100%;max-width:100%;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
   <tbody>
      <tr>
         <td bgcolor="#F4F4F4" align="center">
            <!--container-->
            <table class="row" style="width:600px;max-width:600px;" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
               <tbody>
                  <tr>
                     <td bgcolor="#f6f6f4" align="center">
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
                                                         <td height="30">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td align="center"><img width="100" style="display:block;width:100%;max-width:100px;" alt="img" src="{{ asset('assets/images') }}/cart.png"></td>
                                                      </tr>
                                                      <tr>
                                                         <td height="20">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td align="center" style="font-family:'Josefin Sans', Arial, Helvetica, sans-serif;font-size: 30px;color: #282828;">Order confirmation</td>
                                                      </tr>
                                                      <tr>
                                                         <td height="10"></td>
                                                      </tr>
                                                      <tr>
                                                         <td align="center" style="font-family:'Josefin Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #282828;">Thanks for your Order. We will send tracking 
                                                            info when your order ships.  
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td height="25">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td align="center">
                                                            <!--button-->
                                                            <table border="0" bgcolor="#0052cc" cellpadding="0" cellspacing="0">
                                                               <tbody>
                                                                  <tr>
                                                                     <td align="center" height="40" width="170" style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 13px;color: #ffffff;font-weight: 600;letter-spacing: 0.5px;">
                                                                        <a href="https://Pegasus.app/" target="_blank" style="color: #ffffff">GO TO SITE</a>
                                                                     </td>
                                                                  </tr>
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td height="30">&nbsp;</td>
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
<table style="width:100%;max-width:100%;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
   <tbody>
      <tr>
         <td bgcolor="#F4F4F4" align="center">
            <!--container-->
            <table class="row" style="width:600px;max-width:600px;" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
               <tbody>
                  <tr>
                     <td bgcolor="#FFFFFF" align="center">
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
                                                         <td height="30">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td>
                                                            <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center">
                                                               <tbody>
                                                                  <tr>
                                                                     <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 13px;color: #282828;">Order Number #{{$order['order_no']}}</td>
                                                                     <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 13px;color: #282828;">Order Date {{date('d/m/Y',strtotime($order['created_at']))}}</td>
                                                                     <td width="30">&nbsp;</td>
                                                                     <td align="right" style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 13px;color: #282828;">Items Price</td>
                                                                  </tr>
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td style="line-height: 8px;height: 8px;font-size: 0px;">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td bgcolor="#e7e6e2" style="line-height: 8px;height: 8px;font-size: 0px;">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td height="20">&nbsp;</td>
                                                      </tr>
                                                      <?php $ol = App\Models\OrderProduct::where('order_id',$order['id'])->orderBy('id','ASC')->get(); ?>
                                                      @if($ol->count() > 0)
                                                      <?php $ic = 1; ?>
                                                      @foreach($ol as $a)
                                                      <?php 
                                                         $taxrate1 = '0%';
                                                         $taxrate2 = '0%';
                                                         $taxtype1 = 'None';
                                                         $taxtype2 = 'None';
                                                         $ex = '';
                                                         if ($address2->state == 'Uttar Pradesh' || $address2->state == 'Uttar Pradesh') {
                                                            $txrate = round($a->tax_percentage/2);
                                                            $taxrate1 = $txrate.'%';
                                                            $taxrate2 = $txrate.'%';
                                                            $taxtype1 = 'CGST';
                                                            $taxtype2 = 'SGST';
                                                         }
                                                         else {
                                                            $taxrate1 = $a->tax_percentage.'%';
                                                            $taxrate2 = '';
                                                            $taxtype1 = 'IGST';
                                                            $taxtype2 = '';
                                                         }

                                                         $cover_image = asset('project/public/book/').'/default.png';
                                                         if ($a->type == 1) {
                                                            $book = App\Models\Book::where('status',1)->where('id',$a->book_id)->first();
                                                            if ($book) {
                                                               $cover_image = asset('project/public/book/').'/'.$book->cover_image;
                                                            }
                                                         }
                                                         elseif ($a->type == 2) {
                                                            $book = App\Models\Uniform::where('status',1)->where('id',$a->uniform_id)->first();
                                                            if ($book) {
                                                               $cover_image = asset('project/public/uniform/').'/'.$book->display_image;
                                                            }
                                                         }
                                                         elseif ($a->type == 3) {
                                                            $book = App\Models\Notebook::where('status',1)->where('id',$a->notebook_id)->first();
                                                            if ($book) {
                                                               $cover_image = asset('project/public/notebooks/').'/'.$book->display_image;
                                                            }  
                                                         }
                                                      ?>
                                                      @if($a->type == 1)
                                                      <?php 
                                                         if ($a->type == 1 && $a->book_online_support  == 1) {
                                                            $ex = '(Ebook)';
                                                         }
                                                         else {
                                                            $ex = '(Physical Book)';
                                                            $taxtype1 = 'None';
                                                            $taxtype2 = 'None';
                                                         }
                                                         
                                                      ?>
                                                      @endif
                                                      <tr>
                                                         <td>
                                                            <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center">
                                                               <tbody>
                                                                  <tr>
                                                                     <td width="130"><img width="130" style="display:block;width:100%;max-width:130px;" alt="img" src="{{$cover_image}}"></td>
                                                                     <td width="20">&nbsp;</td>
                                                                     <td width="250">
                                                                        <table border="0" width="100%" cellpadding="0" cellspacing="0">
                                                                           <tbody>
                                                                              <tr>
                                                                                 <td style="font-family:'Josefin Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #282828;line-height: 21px;">{{$a->product_name}}{{$ex != '' ? '|'.$ex : ''}}</td>
                                                                              </tr>
                                                                              <tr>
                                                                                 <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;line-height: 21px;">Quantitiy : {{$a->qty}}</td>
                                                                              </tr>
                                                                           </tbody>
                                                                        </table>
                                                                     </td>
                                                                     <td>&nbsp;</td>
                                                                     <td align="right" width="60">
                                                                        <table border="0" width="60" cellpadding="0" cellspacing="0">
                                                                           <tbody>
                                                                              <tr>
                                                                                 <td align="right" style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #282828;">&#8377;{{number_format((double)($a->total_price - $a->total_tax), 2, '.', '')}}</td>
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
                                                         <td height="20" style="border-bottom: 1px solid #e0e0e0">&nbsp;</td>
                                                      </tr>
                                                      <?php $ic++; ?>
                                                      @endforeach
                                                      @endif
                                                      
                                                      <tr>
                                                         <td height="20">&nbsp;</td>
                                                      </tr>
                                                      
                                                      <tr>
                                                         <td height="20">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td bgcolor="#e7e6e2" style="line-height: 8px;height: 8px;font-size: 0px;">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td height="30">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td align="right">
                                                            <table border="0" width="280" cellpadding="0" cellspacing="0">
                                                               <tbody>
                                                                  <tr>
                                                                     <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;line-height: 24px;">Subtotal</td>
                                                                     <td>&nbsp;</td>
                                                                     <td align="right" style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;">&#8377;{{number_format((double)($order['total_mrp']+$order['delivery_price']-$order['coupon_discount']-$order['tax_amt']), 2, '.', '')}}</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;line-height: 24px;">Shipping</td>
                                                                     <td>&nbsp;</td>
                                                                     <td align="right" style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;">&#8377;{{$order['delivery_price'] ?? 0}}</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;line-height: 24px;">Tax</td>
                                                                     <td>&nbsp;</td>
                                                                     <td align="right" style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;">&#8377;{{number_format((double)$order['tax_amt'], 2, '.', '')}}</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;line-height: 24px;">Discount</td>
                                                                     <td>&nbsp;</td>
                                                                     <td align="right" style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;color: #282828;text-decoration: ">- &#8377;{{$order['coupon_discount'] ?? 0}}</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #282828;font-weight: 600;line-height: 24px;">Total Due</td>
                                                                     <td>&nbsp;</td>
                                                                     <td align="right" style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #282828;"><strong>&#8377;{{number_format((double)($order['net_amount']), 2, '.', '')}}</strong></td>
                                                                  </tr>
                                                                  
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td height="30">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td bgcolor="#e7e6e2" style="line-height: 8px;height: 8px;font-size: 0px;">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="line-height: 15px;height: 15px;font-size: 0px;">&nbsp;</td>
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
<table style="width:100%;max-width:100%;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
   <tbody>
      <tr>
         <td bgcolor="#F4F4F4" align="center">
            <!--container-->
            <table class="row" style="width:600px;max-width:600px;" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
               <tbody>
                  <tr>
                     <?php $address1 = App\Models\OrderAddress::where('order_id',$order['id'])->where('address_type',1)->first();
                           $address2 = App\Models\OrderAddress::where('order_id',$order['id'])->where('address_type',1)->first();
                     ?>
                     <td bgcolor="#FFFFFF" align="center">
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
                                                         <td style="line-height: 15px;height: 15px;font-size: 0px;">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                         <td align="center">
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table border="0" cellpadding="0" cellspacing="0">
                                                               <tr>
                                                                  <td>
                                                                     <![endif]-->
                                                                     <!-- column -->    
                                                                     @if($address1)
                                                                     <table class="row" style="width:260px;max-width:260px;" width="260" cellspacing="0" cellpadding="0" border="0" align="left">
                                                                        <tbody>
                                                                           <tr>
                                                                              <td align="center">
                                                                                 <!-- content -->
                                                                                 <table width="260" style="width:260px;max-width:260px;" cellspacing="0" cellpadding="0" border="0" align="center">
                                                                                    <tbody>
                                                                                       <tr>
                                                                                          <td align="center" bgcolor="#F5F5F3">
                                                                                             <table border="0" width="200" cellpadding="0" cellspacing="0" align="center">
                                                                                                <tbody>
                                                                                                   <tr>
                                                                                                      <td height="30">&nbsp;</td>
                                                                                                   </tr>
                                                                                                   <tr>
                                                                                                      <td style="font-family:'Josefin Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #282828;">Billing Address</td>
                                                                                                   </tr>
                                                                                                   <tr>
                                                                                                      <td height="18">&nbsp;</td>
                                                                                                   </tr>
                                                                                                   <tr>
                                                                                                      <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 13px;color: #282828;line-height: 22px;">{{ucfirst($address1->name)}} <br>{{$address1->address}}{{', '.$address1->locality ?? ''}} <br>{{$address1->city}} {{$address1->pincode}} <br>{{$address1->state}}, {{$address1->country}}, {{$address1->type}}<br>{{$address1->mobile}} </td>
                                                                                                   </tr>
                                                                                                   <tr>
                                                                                                      <td height="30">&nbsp;</td>
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
                                                                     @endif
                                                                     <!--[if (gte mso 9)|(IE)]>
                                                                  </td>
                                                                  <td>
                                                                     <![endif]-->
                                                                     <!-- gap -->
                                                                     <table class="row" style="width:50px;max-width:50px;" width="50" cellspacing="0" cellpadding="0" border="0" align="left">
                                                                        <tbody>
                                                                           <tr>
                                                                              <td height="30"></td>
                                                                           </tr>
                                                                        </tbody>
                                                                     </table>

                                                                     <!--[if (gte mso 9)|(IE)]>
                                                                  </td>
                                                                  <td>
                                                                     <![endif]-->
                                                                     <!-- column --> 
                                                                     @if($address2)
                                                                     <table class="row" style="width:230px;max-width:230px;" width="230" cellspacing="0" cellpadding="0" border="0" align="right">
                                                                        <tbody>
                                                                           <tr>
                                                                              <td align="center">
                                                                                 <!-- content -->
                                                                                 <table width="230" style="width:230px;max-width:230px;" cellspacing="0" cellpadding="0" border="0" align="center">
                                                                                    <tbody>
                                                                                       <tr>
                                                                                          <td height="30">&nbsp;</td>
                                                                                       </tr>
                                                                                       <tr>
                                                                                          <td style="font-family:'Josefin Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #282828;">Shipping Address</td>
                                                                                       </tr>
                                                                                       <tr>
                                                                                          <td height="18">&nbsp;</td>
                                                                                       </tr>
                                                                                       <tr>
                                                                                          <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 13px;color: #282828;line-height: 22px;">{{ucfirst($address2->name)}} <br>{{$address2->address}}{{', '.$address2->locality ?? ''}} <br>{{$address2->city}} {{$address2->pincode}} <br>{{$address2->state}}, {{$address2->country}}, {{$address2->type}}<br>{{$address2->mobile}}</td>
                                                                                       </tr>
                                                                                       <tr>
                                                                                          <td height="30">&nbsp;</td>
                                                                                       </tr>
                                                                                       <tr>
                                                                                          <td style="font-family:'Josefin Sans', Arial, Helvetica, sans-serif;font-size: 18px;color: #282828;">Payment Method</td>
                                                                                       </tr>
                                                                                       <tr>
                                                                                          <td height="18">&nbsp;</td>
                                                                                       </tr>
                                                                                       <tr>
                                                                                          <td style="font-family:'Open Sans', Arial, Helvetica, sans-serif;font-size: 13px;color: #282828;">Online</td>
                                                                                       </tr>
                                                                                       <tr>
                                                                                          <td height="30">&nbsp;</td>
                                                                                       </tr>
                                                                                    </tbody>
                                                                                 </table>
                                                                              </td>
                                                                           </tr>
                                                                        </tbody>
                                                                     </table>
                                                                     @endif
                                                                     <!--[if (gte mso 9)|(IE)]>
                                                                  </td>
                                                               </tr>
                                                            </table>
                                                            <![endif]-->
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td height="30">&nbsp;</td>
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
<table style="width:100%;max-width:100%;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
   <tbody>
      <tr>
         <td bgcolor="#F4F4F4" align="center">
            <!--container-->
            <table class="row" style="width:600px;max-width:600px;" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
               <tbody>
                  <tr>
                     <td bgcolor="#0c1a32" align="center">
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
                                                                        <a href="{{$settings->fb_link}}"><img width="25" style="display:block;width:100%;max-width:25px;" src="{{ asset('assets/emailtemplates') }}/fb.png">
                                                                        </a>
                                                                     </td>
                                                                     <td width="10">&nbsp;</td>
                                                                     <td width="25">
                                                                        <a href="{{$settings->twitter_link}}"><img width="25" style="display:block;width:100%;max-width:25px;" src="{{ asset('assets/emailtemplates') }}/tw.png">
                                                                        </a>
                                                                     </td>
                                                                     <td width="10">&nbsp;</td>
                                                                     <td width="25">
                                                                        <a href="{{$settings->linkedin_link}}"><img width="25" style="display:block;width:100%;max-width:25px;" src="{{ asset('assets/emailtemplates') }}/in.png"></a>
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
                                                         <td align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 19px;">
                                                            Kindly note that this is a system generated email. PLEASE DO NOT REPLY.<br> For any help assistance and support, you can email us on <a href="mailto:{{$settings->support_email}}" target="_blank" style="color: #dadada">{{$settings->support_email}}.</a>
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
                                                                     <td align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 20px;text-decoration: underline"><a href="{{url('/term')}}" target="_blank" style="color: #dadada">Terms & Conditions</a></td>
                                                                     <td width="20" align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 20px;">|</td>
                                                                     <td align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 20px;text-decoration: underline"><a href="{{url('/privacy')}}" target="_blank" style="color: #dadada">Privacy Policy</a></td>
                                                                     <td width="20" align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 20px;">|</td>
                                                                     <td align="center" style="font-family:'Roboto', Arial, Helvetica, sans-serif;font-size: 13px;color: #dadada;line-height: 20px;text-decoration: underline"><a href="{{url('/contact')}}" target="_blank" style="color: #dadada">Help Center</a></td>
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