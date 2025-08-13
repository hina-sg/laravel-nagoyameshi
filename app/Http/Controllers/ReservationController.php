<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Reservation;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::where("user_id", Auth::id())
        ->orderBy("created_at", "desc")
        ->paginate(15);

        return view("reservations.index", compact("reservations"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {
        return view("reservations.create", compact("restaurant"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            "reservation_date" => "required|date_format:Y-m-d",
            "reservation_time" => "required|date_format:H:i",
            "number_of_people" => "required|numeric|between:1,50",
        ]);

        $reservation = new Reservation();
        $reservation->reserved_datetime = $request->input("reservation_date") . " " . $request->input("reservation_time");
        $reservation->number_of_people = $request->input("number_of_people");
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $request->user()->id;
        $reservation->save();

        return redirect()->route("reservations.index")
        ->with(["flash_message" => "予約が完了しました。"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        if($reservation->user_id !== Auth::id()){
            return redirect()->route("reservations.index") ->with(["error_message" => "不正なアクセスです。"]);
        }

        $reservation->delete();

        return redirect()->route("reservations.index")
        ->with(["flash_message" => "予約をキャンセルしました。"]);
    }
}
