<?php

namespace App\Http\Controllers\FrontEnd\Event;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\PaymentGateway\FlutterwaveController;
use App\Http\Controllers\FrontEnd\PaymentGateway\InstamojoController;
use App\Http\Controllers\FrontEnd\PaymentGateway\IyzipayController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MercadoPagoController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MidtransController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MollieController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MyFatoorahController;
use App\Http\Controllers\FrontEnd\PaymentGateway\OfflineController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PayPalController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PaystackController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PaytabsController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PaytmController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PerfectMoneyController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PhonepeController;
use App\Http\Controllers\FrontEnd\PaymentGateway\RazorpayController;
use App\Http\Controllers\FrontEnd\PaymentGateway\StripeController;
use App\Http\Controllers\FrontEnd\PaymentGateway\ToyyibpayController;
use App\Http\Controllers\FrontEnd\PaymentGateway\XenditController;
use App\Http\Controllers\FrontEnd\PaymentGateway\YocoController;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Event\EventDates;
use App\Models\Event\EventImage;
use App\Models\Event\Ticket;
use App\Models\Organizer;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\PHPMailer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
  public function index(Request $request, $id)
  {
    $basic = Basic::select('event_guest_checkout_status')->first();
    if ($basic->event_guest_checkout_status == 0 && $request->type != 'guest') {
      // check whether user is logged in or not
      if (Auth::guard('customer')->check() == false) {
        return redirect()->route('customer.login', ['redirectPath' => 'course_details']);
      }
    }
    $booking = new Booking();
    $booking->customer_id = Auth::guard('customer')->id(); // Get logged-in customer ID
    $booking->booking_id = $request->booking_id; // Assuming this is passed in the request
    $booking->event_id = $id; // Assuming this is the event ID
    $booking->organizer_id = $request->organizer_id; // Get from request
    $booking->fname = $request->fname; // Get from request
    $booking->lname = $request->lname; // Get from request
    $booking->email = $request->email; // Get from request
    $booking->phone = $request->phone; // Get from request
    $booking->country = $request->country; // Get from request
    $booking->state = $request->state; // Get from request
    $booking->city = $request->city; // Get from request
    $booking->zip_code = $request->zip_code; // Get from request
    $booking->address = $request->address; // Get from request
    $booking->variation = $request->variation; // Get from request
    $booking->price = $request->price; // Get from request
    $booking->quantity = $request->quantity; // Get from request
    $booking->discount = $request->discount; // Get from request
    $booking->tax = $request->tax; // Get from request
    $booking->commission = $request->commission; // Get from request
    $booking->early_bird_discount = $request->early_bird_discount; // Get from request
    $booking->currencyText = $request->currencyText; // Get from request
    $booking->currencyTextPosition = $request->currencyTextPosition; // Get from request
    $booking->currencySymbol = $request->currencySymbol; // Get from request
    $booking->currencySymbolPosition = $request->currencySymbolPosition; // Get from request
    $booking->paymentMethod = $request->gateway; // Payment gateway
    $booking->gatewayType = $request->gateway; // Payment gateway type
    $booking->paymentStatus = 'pending'; // Default payment status
    $booking->invoice = $request->invoice; // Get from request
    $booking->attachmentFile = $request->attachmentFile; // Get from request
    $booking->event_date = $request->event_date; // Get from request
    $booking->tax_percentage = $request->tax_percentage; // Get from request
    $booking->commission_percentage = $request->commission_percentage; // Get from request
    $booking->scan_status = 0; // Default to not scanned
    $booking->conversation_id = $request->conversation_id; // Get from request
    $booking->econtact = $request->econtact; // Get from request
    $booking->econtactname = $request->econtactname; // Get from request
    $booking->jsize = $request->tshirtsize;
    $booking->save(); // Save the booking
    echo ("sahil");
    // Redirect or return a response after saving the booking
    // return redirect()->route('booking.success');
    // paym ent
    if ($request->total != 0 || Session::get('sub_total') != 0) {
      // if (!$request->exists('gateway')) {
      //   Session::flash('error', 'Please select a payment method.');

      //   return redirect()->back();
      // } else if ($request['gateway'] == 'paypal') {
      //   $paypal = new PayPalController();

      //   return $paypal->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'razorpay') {
      //   $razorpay = new RazorpayController();

      //   return $razorpay->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'instamojo') {
      //   $instamojo = new InstamojoController();

      //   return $instamojo->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'paystack') {
      //   $paystack = new PaystackController();

      //   return $paystack->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'flutterwave') {
      //   $flutterwave = new FlutterwaveController();

      //   return $flutterwave->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'mercadopago') {
      //   $mercadopago = new MercadoPagoController();

      //   return $mercadopago->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'mollie') {
      //   $mollie = new MollieController();

      //   return $mollie->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'stripe') {
      //   $stripe = new StripeController();

      //   return $stripe->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'paytm') {
      //   $paytm = new PaytmController();

      //   return $paytm->bookingProcess($request, $id);
      // } else if ($request['gateway'] == 'midtrans') {
      //   Session::put('midtrans_payment_type', 'event');
      //   $paytm = new MidtransController();

      //   return $paytm->makePayment($request, $id);
      // } else if ($request['gateway'] == 'iyzico') {
      //   $paytm = new IyzipayController();

      //   return $paytm->makePayment($request, $id);
      // } else if ($request['gateway'] == 'paytabs') {
      //   $paytabs = new PaytabsController();

      //   return $paytabs->makePayment($request, $id);
      // } else if ($request['gateway'] == 'toyyibpay') {
      //   $toyyibpay = new ToyyibpayController();

      //   return $toyyibpay->makePayment($request, $id);
      // } else if ($request['gateway'] == 'phonepe') {
      //   $phonepe = new PhonepeController();

      //   return $phonepe->makePayment($request, $id);
      // } else if ($request['gateway'] == 'yoco') {
      //   $yoco = new YocoController();

      //   return $yoco->makePayment($request, $id);
      // } else if ($request['gateway'] == 'xendit') {
      //   $xindit = new XenditController();

      //   return $xindit->makePayment($request, $id);
      // } else if ($request['gateway'] == 'myfatoorah') {
      //   $xindit = new MyFatoorahController();

      //   return $xindit->makePayment($request, $id);
      // } else if ($request['gateway'] == 'perfect_money') {
      //   $perfect_money = new PerfectMoneyController();

      //   return $perfect_money->makePayment($request, $id);
      // } else {
      //   $offline = new OfflineController();
      //   return $offline->bookingProcess($request, $id);
      // }
    } else {
      try {
        $event = json_decode($request->event, true);
        $arrData = array(
          'event_id' => $event['id'],
          'price' => 0,
          'tax' => 0,
          'commission' => 0,
          'quantity' => $request->quantity,
          'discount' => 0,
          'total_early_bird_dicount' => 0,
          'currencyText' => null,
          'currencyTextPosition' => null,
          'currencySymbol' => null,
          'currencySymbolPosition' => null,
          'fname' => $request->fname,
          'lname' => $request->lname,
          'email' => $request->email,
          'phone' => $request->phone,
          'country' => $request->country,
          'state' => $request->state,
          'city' => $request->city,
          'zip_code' => $request->city,
          'address' => $request->address,
          'paymentMethod' => null,
          'gatewayType' => null,
          'paymentStatus' => 'free',
          'event_date' => Session::get('event_date')
        );

        $bookingInfo = $this->storeData($arrData);

        // generate an invoice in pdf format
        $invoice = $this->generateInvoice($bookingInfo, $event['id']);
        //unlink qr code
        @mkdir(public_path('assets/admin/qrcodes/'), 0775, true);
        @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');
        //end unlink qr code

        // then, update the invoice field info in database
        $bookingInfo->update(['invoice' => $invoice]);

        // send a mail to the customer with the invoice
        $this->sendMail($bookingInfo);

        $request->session()->forget('event_id');
        $request->session()->forget('selTickets');
        $request->session()->forget('arrData');
        $request->session()->forget('discount');

        return redirect()->route('event_booking.complete', ['id' => $event['id'], 'booking_id' => $bookingInfo->id, 'via' => 'offline']); //code...
      } catch (\Throwable $th) {
        return view('errors.404');
      }
    }
  }

  public function storeData($info)
  {
    try {
      $event = Event::find($info['event_id']);

      if ($event) {
        if ($event->organizer_id) {
          $organizer_id = $event->organizer_id;
        } else {
          $organizer_id = null;
        }
      }
      $variations = Session::get('selTickets');

      if ($variations) {
        foreach ($variations as $variation) {

          $ticket = Ticket::where('id', $variation['ticket_id'])->first();
          if ($ticket->pricing_type == 'normal' && $ticket->ticket_available_type == 'limited') {
            if ($ticket->ticket_available - $variation['qty'] >= 0) {
              $ticket->ticket_available = $ticket->ticket_available - $variation['qty'];
              $ticket->save();
            }
          } elseif ($ticket->pricing_type == 'variation') {
            $ticket_variations = json_decode($ticket->variations, true);
            $update_variation = [];
            foreach ($ticket_variations as $ticket_variation) {
              if ($ticket_variation['name'] == $variation['name']) {

                if ($ticket_variation['ticket_available_type'] == 'limited') {
                  $ticket_available = intval($ticket_variation['ticket_available']) - intval($variation['qty']);
                } else {
                  $ticket_available = $ticket_variation['ticket_available'];
                }

                $update_variation[] = [
                  'name' => $ticket_variation['name'],
                  'price' => round($ticket_variation['price'], 2),
                  'ticket_available_type' => $ticket_variation['ticket_available_type'],
                  'ticket_available' => $ticket_available,
                  'max_ticket_buy_type' => $ticket_variation['max_ticket_buy_type'],
                  'v_max_ticket_buy' => $ticket_variation['v_max_ticket_buy'],
                ];
              } else {
                $update_variation[] = [
                  'name' => $ticket_variation['name'],
                  'price' => round($ticket_variation['price'], 2),
                  'ticket_available_type' => $ticket_variation['ticket_available_type'],
                  'ticket_available' => $ticket_variation['ticket_available'],
                  'max_ticket_buy_type' => $ticket_variation['max_ticket_buy_type'],
                  'v_max_ticket_buy' => $ticket_variation['v_max_ticket_buy'],
                ];
              }
            }
            $ticket->variations = json_encode($update_variation, true);

            $ticket->save();
          } elseif ($ticket->pricing_type == 'free' && $ticket->ticket_available_type == 'limited') {
            if ($ticket->ticket_available - $variation['qty'] >= 0) {
              $ticket->ticket_available = $ticket->ticket_available - $variation['qty'];
              $ticket->save();
            }
          }
        }
        $variations = json_encode(Session::get('selTickets'), true);
      } else {
        $ticket = $event->ticket()->first();
        $ticket->ticket_available = $ticket->ticket_available - (int) $info['quantity'];
        $ticket->save();
      }

      $basic = Basic::where('uniqid', 12345)->select('tax', 'commission')->first();

      $booking = Booking::create([
        'customer_id' => Auth::guard('customer')->user() ? Auth::guard('customer')->user()->id : null,
        'booking_id' => uniqid(),
        'fname' => $info['fname'],
        'lname' => $info['lname'],
        'email' => $info['email'],
        'phone' => $info['phone'],
        'country' => $info['country'],
        'state' => $info['state'],
        'city' => $info['city'],
        'zip_code' => $info['zip_code'],
        'address' => $info['address'],
        'event_id' => $info['event_id'],
        'organizer_id' => $organizer_id,
        'variation' => $variations,
        'price' => round($info['price'], 2),
        'tax' => round($info['tax'], 2),
        'commission' => round($info['commission'], 2),
        'tax_percentage' => $basic->tax,
        'commission_percentage' => $basic->commission,
        'quantity' => $info['quantity'],
        'discount' => round($info['discount'], 2),
        'early_bird_discount' => round($info['total_early_bird_dicount'], 2),
        'currencyText' => $info['currencyText'],
        'currencyTextPosition' => $info['currencyTextPosition'],
        'currencySymbol' => $info['currencySymbol'],
        'currencySymbolPosition' => $info['currencySymbolPosition'],
        'paymentMethod' => $info['paymentMethod'],
        'gatewayType' => $info['gatewayType'],
        'paymentStatus' => $info['paymentStatus'],
        'invoice' => array_key_exists('attachmentFile', $info) ? $info['attachmentFile'] : null,
        'attachmentFile' => array_key_exists('attachmentFile', $info) ? $info['attachmentFile'] : null,
        'event_date' => Session::get('event_date'),
        'conversation_id' => array_key_exists('conversation_id', $info) ? $info['conversation_id'] : null,
      ]);
      return $booking;
    } catch (\Exception $th) {
    }
  }


  public function complete(Request $request)
  {
    $language = $this->getLanguage();

    Session::forget('selTickets');
    Session::forget('total');
    Session::forget('quantity');
    Session::forget('total_early_bird_dicount');
    Session::forget('event');

    $id = $request->id;
    $booking_id = $request->booking_id;

    $booking = Booking::where('id', $booking_id)->first();
    $information['booking'] = $booking;
    $event = Event::where('id', $id)->with([
      'information' => function ($query) use ($language) {
        return $query->where('language_id', $language->id)->first();
      }
    ])->first();
    $information['event'] = $event;
    if ($event->date_type == 'multiple') {
      $start_date_time = strtotime($booking->event_date);
      $start_date_time = date('Y-m-d H:i:s', $start_date_time);

      $event_date = EventDates::where('start_date_time', $start_date_time)->where('event_id', $id)->first();

      $information['event_date'] = $event_date;
    }
    return view('frontend.payment.success', $information);
  }

  public function cancel($id, Request $request)
  {
    return redirect()->route('check-out');
  }

  public function sendMail($bookingInfo)
  {
    // first get the mail template info from db
    $mailTemplate = MailTemplate::where('mail_type', 'event_booking')->first();
    $mailSubject = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // second get the website title & mail's smtp info from db
    $info = DB::table('basic_settings')
      ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    $customerName = $bookingInfo->fname . ' ' . $bookingInfo->lname;
    $orderId = $bookingInfo->booking_id;

    $language = $this->getLanguage();
    $eventContent = EventContent::where('event_id', $bookingInfo->event_id)->where('language_id', $language->id)->first();
    $eventTitle = $eventContent ? $eventContent->title : '';

    $websiteTitle = $info->website_title;

    $mailBody = str_replace('{customer_name}', $customerName, $mailBody);
    $mailBody = str_replace('{order_id}', $orderId, $mailBody);
    $mailBody = str_replace('{title}', '<a href="' . route('event.details', [$eventContent->slug, $eventContent->event_id]) . '">' . $eventTitle . '</a>', $mailBody);
    $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);

    // initialize a new mail
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // if smtp status == 1, then set some value for PHPMailer
    if ($info->smtp_status == 1) {
      $mail->isSMTP();
      $mail->Host = $info->smtp_host;
      $mail->SMTPAuth = true;
      $mail->Username = $info->smtp_username;
      $mail->Password = $info->smtp_password;

      if ($info->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }

      $mail->Port = $info->smtp_port;
    }

    // finally add other informations and send the mail
    try {
      // Recipients
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($bookingInfo->email);

      // Attachments (Invoice)
      $mail->addAttachment(public_path('assets/admin/file/invoices/') . $bookingInfo->invoice);

      // Content
      $mail->isHTML(true);
      $mail->Subject = $mailSubject;
      $mail->Body = $mailBody;

      $mail->send();

      return;
    } catch (\Exception $e) {
      return session()->flash('error', 'Mail could not be sent! Mailer Error: ' . $e);
    }
  }
  public function generateInvoice($bookingInfo, $eventId)
  {
    try {
      $fileName = $bookingInfo->booking_id . '.pdf';
      $directory = public_path('assets/admin/file/invoices/');

      @mkdir($directory, 0775, true);

      $fileLocated = $directory . $fileName;

      //generate qr code
      @mkdir(public_path('assets/admin/qrcodes/'), 0775, true);
      QrCode::size(200)->generate($bookingInfo->booking_id, public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');

      //generate qr code end

      // get course title
      $language = $this->getLanguage();

      $eventInfo = EventContent::where('event_id', $bookingInfo->event_id)->where('language_id', $language->id)->first();

      $width = "50%";
      $float = "right";
      $mb = "35px";
      $ml = "18px";

      PDF::loadView('frontend.event.invoice', compact('bookingInfo', 'eventInfo', 'width', 'float', 'mb', 'ml'))->save($fileLocated);

      return $fileName;
    } catch (\Exception $th) {
    }
  }
}
