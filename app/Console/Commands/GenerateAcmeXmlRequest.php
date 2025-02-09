<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Contracts\RequestMapperInterface;
use App\DTOs\InsuranceRequestData;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class GenerateAcmeXmlRequest extends Command
{
    protected $signature = 'acme:generate-request {filepath : Path to JSON input file}';
    protected $description = 'Generate an XML request for ACME insurance provider';

    private RequestMapperInterface $mapper;

    public function __construct(RequestMapperInterface $mapper)
    {
        parent::__construct();
        $this->mapper = $mapper;
    }

    public function handle(): int
    {
        $filepath = $this->argument('filepath');

        if (!File::exists($filepath)) {
            $this->error("File not found: $filepath");
            return SymfonyCommand::FAILURE;
        }

        $jsonContent = File::get($filepath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON format in file: $filepath");
            return SymfonyCommand::FAILURE;
        }

        $validator = Validator::make($data, [
            'holder'               => 'required|string',
            'prevInsurance_exists' => 'required|string|in:YES,NO',
            'prevInsurance_years'  => 'required|integer|min:0',
            'occasionalDriver'     => 'nullable|string|in:YES,NO',
        ]);

        if ($validator->fails()) {
            $this->error("Validation failed:");
            foreach ($validator->errors()->all() as $error) {
                $this->error("- $error");
            }
            return SymfonyCommand::FAILURE;
        }

        $insuranceRequest = InsuranceRequestData::fromArray($data);

        $xmlString = $this->mapper->map($insuranceRequest);

        $this->info("Generated XML:");
        $this->line($xmlString);

        return SymfonyCommand::SUCCESS;
    }
}
