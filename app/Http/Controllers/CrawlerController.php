<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\CompanyProfile;
use App\Models\CompanyResearchCandidate;

class CrawlerController extends Controller
{
    public function saveCrawledData()
    {
        DB::transaction(function () {
            // Aggiorna task_id nella tabella crawler_data
            DB::table('crawler_data')
                ->join('tasks', 'tasks.name', '=', 'crawler_data.task')
                ->whereNotNull('crawler_data.email')
                ->where('crawler_data.email', '<>', '[]')
                ->whereNotNull('crawler_data.task')
                ->where('crawler_data.agenzia', '=', 0)
                ->update(['crawler_data.task_id' => DB::raw('tasks.id')]);

            // Trasferisci i dati in `companies` e `companies_profiles`
            $crawlerDataRows = DB::table('crawler_data')
                ->whereNotNull('task_id')
                ->get(['Azienda as name', 'luogo as address', 'email', 'task_id', 'titolo']);

            foreach ($crawlerDataRows as $data) {
                // Gestisci l'email
                $primaryEmail = is_array($data->email) ? trim(str_replace(["[", "]", "'"], '', $data->email[0])) : trim(str_replace(["[", "]", "'"], '', $data->email));

                // Se l'email non esiste in `companies`, crea una nuova entry
                if (!Company::where('email', $primaryEmail)->exists()) {
                    $company = new Company();
                    $company->name = $data->name;
                    $company->email = $primaryEmail;
                    $company->password = Hash::make(Str::random(10));
                    $company->is_company = 0;
                    $company->email_verified_at = now();
                    $company->save();
                }

                // Ottieni l'id dell'azienda, crea o aggiorna il profilo aziendale
                $company = Company::where('name', $data->name)->first();
                if ($company) {
                    $randomIva = '00000000000';

                    CompanyProfile::updateOrCreate(
                        ['company_id' => $company->id],
                        [
                            'name' => $data->name,
                            'address' => $data->address,
                            'iva' => $randomIva,
                            // Resto dei campi null o con valori di default
                            'address_GPS_lat' => null,
                            'address_GPS_lon' => null,
                            'referente' => 'CRAWLED',
                            'referenteruolo' => 'CRAWLED',
                            'referenteemail' => 'CRAWLED',
                            'mobile' => 'CRAWLED',
                            'content' => 'CRAWLED',
                            'company_website' => null,
                            'company_logo' => null,
                            // Altri campi...
                        ]
                    );
                }

                // Trasferisci i dati in `company_research_candidates`
                if ($company) {
                    $addressGPSData = getLatLong($data->address);
                    $latitude = $addressGPSData ? $addressGPSData['latitude'] : null;
                    $longitude = $addressGPSData ? $addressGPSData['longitude'] : null;

                    CompanyResearchCandidate::create([
                        'search_type' => 2,
                        'status_id' => 1,
                        'company_id' => $company->id,
                        'task_id' => $data->task_id,
                        'comune' => $data->address ?? null,
                        'GPS_lat' => $latitude,
                        'GPS_lon' => $longitude,
                        'content' => $data->titolo ?? null,
                        'compenso' => null,
                        // Imposta altri campi se necessario
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Crawled data saved successfully']);
    }
}
