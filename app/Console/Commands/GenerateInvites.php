<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;    

class GenerateInvites extends Command
{
    protected $signature = 'generate:invites 
                            {--csv= : Path to CSV file} 
                            {--template= : Path to invite image template} 
                            {--output= : Output folder path}';

    protected $description = 'Generate QR codes and final invites from CSV input';

    /*
    php artisan generate:invites \
    --csv=E:/xampp-8/htdocs/teamwork/thisdatethatyear2/public/invites/user_list.csv \
    --template=E:/xampp-8/htdocs/teamwork/thisdatethatyear2/public/invites/invite_template.jpg \
    --output=E:/xampp-8/htdocs/teamwork/thisdatethatyear2/public/invites/output
    */

    public function handle()
    {
        $csvPath = $this->option('csv');
        $templatePath = $this->option('template');
        $outputBase = $this->option('output');

        if (!$csvPath || !$templatePath || !$outputBase) {
            $this->error('Please provide --csv, --template, and --output options.');
            return Command::FAILURE;
        }

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found at: {$csvPath}");
            return Command::FAILURE;
        }

        if (!file_exists($templatePath)) {
            $this->error("Template image not found at: {$templatePath}");
            return Command::FAILURE;
        }

        @mkdir($outputBase, 0777, true);

        $rows = array_map('str_getcsv', file($csvPath));
        // dd($rows);
        $headers = array_map('strtolower', array_shift($rows));

        // dd($headers);

        foreach ($rows as $row) {
            $data = array_combine($headers, $row);

            // dd($data);

            $name = trim($data['name']);            
            $phone = trim($data['phone']);

            // dd($name, $phone);

            $folderName = $this->sanitize("{$name}_{$phone}");
            $folderPath = $outputBase . '/' . $folderName;
            @mkdir($folderPath, 0777, true);

            $qrText = "You're invited!\nName: {$name}\nPhone: {$phone}";
            $qrPath = "{$folderPath}/qr.png";

            // ✅ Correct way to build QR code in v6.x
            $qrCode = new QrCode($qrText);
            // $qrCode->setSize(300);
            // $qrCode->setMargin(10); 

            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            file_put_contents($qrPath, $result->getString());

            // // ✅ Merge QR on invite image
            $invite = imagecreatefromjpeg($templatePath);
            $qr = imagecreatefrompng($qrPath);

            $qrResized = imagecreatetruecolor(200, 200);
            imagecopyresampled($qrResized, $qr, 0, 0, 0, 0, 200, 200, imagesx($qr), imagesy($qr));

            // Position QR code in center left
            $x = 70; // 30 pixels from left edge
            $y = ((imagesy($invite) - 200) / 2) - 55; // Center vertically
            imagecopy($invite, $qrResized, $x, $y, 0, 0, 200, 200);

            $finalPath = "{$folderPath}/final_invite.jpg";
            imagejpeg($invite, $finalPath, 100);





            // // bopttom right
            // $qrResized = imagecreatetruecolor(200, 200);
            // imagecopyresampled($qrResized, $qr, 0, 0, 0, 0, 200, 200, imagesx($qr), imagesy($qr));
            
            // $x = imagesx($invite) - 200 - 30;
            // $y = imagesy($invite) - 200 - 30;
            // imagecopy($invite, $qrResized, $x, $y, 0, 0, 200, 200);
            
            // $finalPath = "{$folderPath}/final_invite.jpg";
            // imagejpeg($invite, $finalPath, 100);









            imagedestroy($invite);
            imagedestroy($qr);
            imagedestroy($qrResized);

            $this->info("✅ Invite generated for: {$name}");
        }

        $this->info('🎉 All invites generated successfully!');
        return Command::SUCCESS;
    }

    private function sanitize($string)
    {
        return preg_replace('/[^A-Za-z0-9]/', '_', $string);
    }
}
