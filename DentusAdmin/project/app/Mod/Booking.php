<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BookingRemedy;
use App\Models\BookingFeedback;
use App\Models\Mantra;
use App\Models\Rudrakshi;
use App\Models\SemiPreciousStone;
use App\Models\PreciousStone;
use App\Models\MasterDonation;
use App\Models\ThreadColor;



class Booking extends Model
{
    use HasFactory;

    public function memberdetails()
    {
        return $this->belongsTo(UserMember::class,'member_id');
    }

    public function astrologerdetails()
    {
        return $this->belongsTo(Astrologer::class,'astrologer_id');
    }

    public function userdetails()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function remedydetails($id)
    {
        $remedy = array();
        $check = BookingRemedy::where('booking_id',$id)->where('status',1)->where('remedie_for',1)->first();
        if ($check) {
            $mantra = explode(',', $check->mantras_ids);
            $mantraarray = array();
            if (count($mantra) > 0) {
                for ($i=0; $i < count($mantra); $i++) { 
                    $m = Mantra::select('title','description','mantra')->where('id',$mantra[$i])->where('status',1)->first();
                    if ($m) {
                        array_push($mantraarray, $m);
                    }
                }
            }
            $threadcolor = explode(',', $check->thread_colors_ids);
            $threadcolorarray = array();
            if (count($threadcolor) > 0) {
                for ($i=0; $i < count($threadcolor); $i++) { 
                    $tc = ThreadColor::select('title','description')->where('id',$threadcolor[$i])->where('status',1)->first();
                    if ($tc) {
                        array_push($threadcolorarray, $tc);
                    }
                }
            }
            $donations = explode(',', $check->donations_ids);
            $donationscolorarray = array();
            if (count($threadcolor) > 0) {
                for ($i=0; $i < count($donations); $i++) { 
                    $don = MasterDonation::select('title','description')->where('id',$donations[$i])->where('status',1)->first();
                    if ($don) {
                        array_push($donationscolorarray, $don);
                    }
                }
            }
            $rudrakshi = explode(',', $check->rudrakshi_ids);
            $rudrakshicolorarray = array();
            if (count($threadcolor) > 0) {
                for ($i=0; $i < count($rudrakshi); $i++) { 
                    $rud = Rudrakshi::select('title','description')->where('id',$rudrakshi[$i])->where('status',1)->first();
                    if ($rud) {
                        array_push($rudrakshicolorarray, $rud);
                    }
                }
            }
            $precious_stone = explode(',', $check->precious_stone_ids);
            $precious_stonecolorarray = array();
            if (count($threadcolor) > 0) {
                for ($i=0; $i < count($precious_stone); $i++) { 
                    $pres = PreciousStone::select('title','description')->where('id',$precious_stone[$i])->where('status',1)->first();
                    if ($pres) {
                        array_push($precious_stonecolorarray, $pres);
                    }
                }
            }

            $semi_precious_stone = explode(',', $check->semi_precious_stone_ids);
            $semi_precious_stonecolorarray = array();
            if (count($threadcolor) > 0) {
                for ($i=0; $i < count($semi_precious_stone); $i++) { 
                    $semipres = SemiPreciousStone::select('title','description')->where('id',$semi_precious_stone[$i])->where('status',1)->first();
                    if ($semipres) {
                        array_push($semi_precious_stonecolorarray, $semipres);
                    }
                }
            }
            $remedy = $check;
            $remedy->mantra = $mantraarray;
            $remedy->thread_color = $threadcolorarray;
            $remedy->donation = $donationscolorarray;
            $remedy->rudrakshis = $rudrakshicolorarray;
            $remedy->precious_stones = $precious_stonecolorarray;
            $remedy->semi_precious_stones = $semi_precious_stonecolorarray;


        }
        return $remedy;
    }

    public function feedbackdetails($id)
    {
        $feedback = array();
        $check = BookingFeedback::where('booking_id',$id)->where('status',1)->first();
        if ($check) {
            $feedback = $check;
        }
        return $feedback;
    }

    
}
