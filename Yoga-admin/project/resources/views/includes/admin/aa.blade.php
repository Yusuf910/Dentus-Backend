
 <section class="faq-inner-page">
    <div class="container">
       <div class="row">
          <div class="col-md-12">
             <div class="section-inner-header text-center">
                <h2>Frequently Asked Questions</h2>
             </div>
          </div>
       </div>
       <?php
         $faqs = DB::table('faqs')
             ->whereNotIn('status', [2])
             ->orderBy('updated_at', 'ASC')
             ->get();
         ?>
       <div class="row">
          <div class="col-lg-6 col-md-6">
             <div class="faq-info faq-inner-info">
                <div class="accordion" id="faq-details">
                  @if (!$faqs->isEmpty())
                  @php $halfCount = ceil($faqs->count() / 2); @endphp
                  @foreach ($faqs->take($halfCount) as $key => $faq)
                   <div class="accordion-item">
                      <h2 class="accordion-header" id="headingFive{{ $key }}">
                         <a class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive{{ $key }}" aria-expanded="false" aria-controls="collapseFive{{ $key }}">
                            {{ $faq->title ?? "" }}
                        </a>
                      </h2>
                      <div id="collapseFive{{ $key }}" class="accordion-collapse collapse" aria-labelledby="headingFive{{ $key }}" data-bs-parent="#faq-details">
                         <div class="accordion-body">
                            <div class="accordion-content">
                                <p>{{ $faq->description ?? "" }}</p>
                            </div>
                         </div>
                      </div>
                   </div>
                   @endforeach
                  @endif
                </div>
             </div>
          </div>
          <div class="col-lg-6 col-md-6">
            <div class="faq-info faq-inner-info">
               <div class="accordion" id="faq-details-info">
                  @if (!$faqs->isEmpty())
                  @foreach ($faqs->skip($halfCount) as $key => $faq)
                  <div class="accordion-item">
                     <h2 class="accordion-header" id="headingTwo{{ $key }}">
                        <a class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo{{ $key }}" aria-expanded="false" aria-controls="collapseTwo{{ $key }}">
                        {{ $faq->title ?? "" }}
                        </a>
                     </h2>
                     <div id="collapseTwo{{ $key }}" class="accordion-collapse collapse" aria-labelledby="headingTwo{{ $key }}" data-bs-parent="#faq-details-info">
                        <div class="accordion-body">
                           <div class="accordion-content">
                              <p>{{ $faq->description ?? "" }}</p>
                           </div>
                        </div>
                     </div>
                  </div>
                  @endforeach
                  @endif
               </div>
            </div>
         </div>
       </div>
    </div>
 </section>