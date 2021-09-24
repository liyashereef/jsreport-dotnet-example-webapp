<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\EmailAccountsMaster;
use Carbon\Carbon;
use DB;
use App\Services\HelperService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class EmailAccountsRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    public function __construct(EmailAccountsMaster $emailAccountsMaster, HelperService $helperService)
    {
        $this->emailAccountsMaster = $emailAccountsMaster;
        $this->helperService = $helperService;

    }

    public function getAll(){
        return $this->emailAccountsMaster->get();
    }

    public function getSingleEmailAccount($id){
        $data = $this->emailAccountsMaster->find($id);
        return $data;
    }

    public function getEmailAccountsList(){
        $data = $this->emailAccountsMaster->get();
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {
            $each_row["id"] = $each_record->id;
            $each_row["display_name"] = $each_record->display_name;
            $each_row["email_address"] = $each_record->email_address;
            $each_row["user_name"] = $each_record->user_name;
            $each_row["smtp_server"] = $each_record->smtp_server;
            $each_row["port"] = $each_record->port;
            if($each_record->encryption == 1){
                $each_row["encryption"] = "SSL";
            }else if($each_record->encryption == 2){
                $each_row["encryption"] = "TLS";
            }else{
                $each_row["encryption"] = "None";
            }
            $each_row["default"] = $each_record->default;
            array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;
    }

    public function save($request){

        $data = [
            'display_name' => $request['display_name'],
            'smtp_server' => $request['smtp_server'],
            'port' => $request['port'],
            'email_address' => $request['email_address'],
            'user_name' => $request['user_name'],
            'password' => $request['password'],
            'default' => isset($request['default']) ? 1 : 0,
            'encryption' => $request['encryption'],
            ];

        if($data['default'] == 1 ){
            DB::table('email_accounts_masters')->update(['default' => 0]);
        }

        $to = $data['email_address'];
        $mail_content = [
            'subject' => "Email Account Validation",
            'body' => "Your Email is valid"
        ];

        $mail = $this->sendNotification($data, $mail_content, $to);

        if ($mail) {
            if($request['id'] == null){
                return $this->emailAccountsMaster->create($data);
            }else{
                return $this->emailAccountsMaster->updateOrCreate(['id' => $request['id']], $data);
            }

        } else {

            return ["success"=>false,"message"=>"Invalid credentials"];

        }


    }

    public function sendNotification($data, $mail_content, $to)
    {
        $mail_from_name = config("mail.from.name");
        $mail_from_address = config("mail.from.address");
        try {

            $transport = (new \Swift_SmtpTransport($data["smtp_server"], $data["port"]))
                ->setUsername($data["user_name"])
                ->setPassword($data["password"]);

            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);
            // Create a message
            $message = (new \Swift_Message($mail_content['subject']))
                ->setFrom($mail_from_address, $mail_from_name)
                ->setTo([$to])
                ->setBody($mail_content['body'], 'text/html');

            // Send the message
            $result = $mailer->send($message);
            return $result;

            } catch (\Throwable $th) {
                throw $th;

            }

    }

    public function delete($id)
    {
         return $this->emailAccountsMaster->destroy($id);
    }



}
?>
