@extends('layouts.admin')
@section('content')
<?php 
$c1 = '';
$u1 = auth()->user()->id;
if (isset($_GET['c1'])) 
{
   $c1 = $_GET['c1'];
}
if (isset($_GET['u1'])) 
{
   $u1 = $_GET['u1'];
}
?>
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">My Ratings</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-body pt-3">
               <style type="text/css">
                  .rating-block{
                  background-color:#FAFAFA;
                  border:1px solid #EFEFEF;
                  padding:15px 15px 20px 15px;
                  border-radius:3px;
                  }
                  .bold{
                  font-weight:700;
                  }
                  .padding-bottom-7{
                  padding-bottom:7px;
                  }
                  .review-block{
                  background-color:#FAFAFA;
                  border:1px solid #EFEFEF;
                  padding:15px;
                  border-radius:3px;
                  margin-bottom:15px;
                  }
                  .review-block img{
                     width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
                  }
                  .review-block-name{
                  font-size:12px;
                  margin:10px 0;
                  }
                  .review-block-date{
                  font-size:12px;
                  }
                  .review-block-rate{
                  font-size:13px;
                  margin-bottom:15px;
                  }
                  .review-block-title{
                  font-size:15px;
                  font-weight:700;
                  margin-bottom:10px;
                  }
                  .review-block-description{
                  font-size:13px;
                  }
               </style>
               <div class="container">
                  <div class="row">
                    <div class="row table-search-blk">
                     <form method="GET" action="{{ route('admin.myuser.feedback') }}">
                        <div class="row">
                           @if(auth()->user()->parent_id == 0)
                           <div class="col-md-2">
                               <div class="mb-3">
                               <label for="average_duration" class="form-label">Doctor</label>
                               <select name="u1" class="form-control">
                                 <option value="">Select</option>
                                 <option {{$u1 == auth()->user()->id ? 'selected' : ''}} value="{{auth()->user()->id}}">{{auth()->user()->name}}</option>
                                 @if($u->count() > 0)
                                 @foreach($u as $ab1)
                                    <option {{$u1 == $ab1->id ? 'selected' : ''}} value="{{$ab1->id}}">{{$ab1->name}}</option>
                                 @endforeach
                                 @endif
                                 </select>
                               </div>
                           </div> 
                           
                           <div class="col-md-2">
                               
                               <button type="submit" class="btn btn-success" alt="alert" id="sa-success">Search</button>
                               
                           </div>
                           @endif
                        </div>
                     </form>
                  </div>
                     <div class="card p-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-3">Overall Rating</h4>
                                <span class="display-4 fw-bold">{{number_format($doctor->ratings_avg_rating, 1)}}</span>
                                <div>
                                    <span class="text-warning">&#9733; &#9733; &#9733; &#9733; &#9734;</span>
                                    <p class="mb-0">{{$totalReviews}} Reviews</p>
                                </div>
                            </div>
                            <div class="ms-3 w-50">
                                <div class="d-flex align-items-center">
                                    <span>5 &#9733;</span>
                                    <div class="progress flex-grow-1 mx-2">
                                        <div class="progress-bar bg-warning" style="width: {{$starCounts['5_star']}}%;"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span>4 &#9733;</span>
                                    <div class="progress flex-grow-1 mx-2">
                                        <div class="progress-bar bg-warning" style="width: {{$starCounts['4_star']}}%;"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span>3 &#9733;</span>
                                    <div class="progress flex-grow-1 mx-2">
                                        <div class="progress-bar bg-warning" style="width: {{$starCounts['3_star']}}%;"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span>2 &#9733;</span>
                                    <div class="progress flex-grow-1 mx-2">
                                        <div class="progress-bar bg-warning" style="width: {{$starCounts['2_star']}}%;"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span>1 &#9733;</span>
                                    <div class="progress flex-grow-1 mx-2">
                                        <div class="progress-bar bg-warning" style="width: {{$starCounts['1_star']}}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <hr/>
                        <div class="review-block">
                           @if($ratingsList)
                           @foreach($ratingsList as $r)
                           <?php $u = App\Models\UserModels\UserProfile::where('id',$r->user_id)->first(); ?>
	                           <div class="row">
	                              <div class="col-sm-3">
	                                 <img src="{{asset('content/user').'/'.$u->image ?? 'default.png'}}" class="img-rounded">
	                                 <div class="review-block-name"><a href="#">{{$u->name ?? 'User'}}</a></div>
	                                 <div class="review-block-date">{{$r->human_date}}</div>
	                              </div>
	                              <div class="col-sm-9">
	                                 <div class="review-block-rate">
									    @php
									        $rating = intval($r->rating); // Convert rating to integer
									        $maxStars = 5; // Total stars
									    @endphp

									    @for ($i = 1; $i <= $maxStars; $i++)
									        @if ($i <= $rating)
									            <i class="fas fa-star text-warning fa-lg"></i> <!-- Filled Star -->
									        @else
									            <i class="far fa-star text-warning fa-lg"></i> <!-- Empty Star -->
									        @endif
									    @endfor
									</div>
	                                 <div class="review-block-title">{{$r->review}}</div>
	                                 
	                              </div>
	                           </div>
	                           <hr/>
                           @endforeach
                           @endif
                           
                        </div>
                     </div>
                  </div>
               </div>
            </div></div>
         </div>
      </div>
   </div>
</div>
@section('js_user_page')
@endsection
@endsection