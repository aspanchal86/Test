<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    /**
     * In the task we need to calculate amount of hours suppliers are working during last week for marketing.
     * You can use any way you like to do it, but remember, in real life we are about to have 400+ real
     * suppliers.
     *
     * @return void
     */
    public function testCalculateAmountOfHoursDuringTheWeekSuppliersAreWorking()
    {
        $response = $this->get('/api/suppliers');
        $hours = NAN;

        $supplierList = \json_decode($response->getContent(), true)['data']['suppliers'][0];
        $amountOfMinutes = 0;
        $weeks = ['mon','tue','wed','thu','fri','sat','sun'];
        foreach ($weeks as $weekday){
            $dayWork = explode(': ',$supplierList[$weekday]);
            $dayTime = explode(',',last($dayWork));
            foreach ($dayTime as $time){
               $slotTime = explode('-',$time);
                $startTime = Carbon::parse(current($slotTime));
                $endTime = Carbon::parse(last($slotTime));
                $minuts = $endTime->diffInMinutes($startTime);
                $amountOfMinutes += $minuts;
            }
        }
        $totalAmountOfHours = $amountOfMinutes / 60;

        $response->assertStatus(200);
        $this->assertEquals(40, $totalAmountOfHours,
            "Our suppliers are working $totalAmountOfHours hours per week in total. Please, find out how much they work..");
    }

    /**
     * Save the first supplier from JSON into database.
     * Please, be sure, all asserts pass.
     *
     * After you save supplier in database, in test we apply verifications on the data.
     * On last line of the test second attempt to add the supplier fails. We do not allow to add supplier with the same name.
     */
    public function testSaveSupplierInDatabase()
    {
        Supplier::query()->truncate();
        $responseList = $this->get('/api/suppliers');
        $supplier = \json_decode($responseList->getContent(), true)['data']['suppliers'][0];

        $response = $this->post('/api/suppliers', $supplier);

       /* $insert = new Supplier();
        $insert->name = $supplier['name'];
        $insert->info = $supplier['info'];
        $insert->rules = $supplier['rules'];
        $insert->district = $supplier['district'];
        $insert->url = $supplier['url'];
        $insert->save();*/
        $insert = Supplier::updateOrCreate(['name'=>$supplier['name']],[
            'info' => $supplier['info'],
            'rules' => $supplier['rules'],
            'district' => $supplier['district'],
            'url' => $supplier['url'],
        ]);
        $response->assertStatus(200);
        $this->assertEquals(1, Supplier::query()->count());
        $dbSupplier = Supplier::first();

        $this->assertNotFalse(curl_init($dbSupplier->url));
        $this->assertNotFalse(curl_init($dbSupplier->rules));
        $this->assertGreaterThan(4, strlen($dbSupplier->info));
        $this->assertNotNull($dbSupplier->name);
        $this->assertNotNull($dbSupplier->district);


        $response = $this->post('/api/suppliers', $supplier);
        $response->assertStatus(422);
    }
}
