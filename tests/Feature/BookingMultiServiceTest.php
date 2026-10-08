<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Slot;
use App\Models\Stylist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingMultiServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_services_filter_only_slots_that_fit_total_duration(): void
    {
        $user = User::factory()->create();
        $shortSlotStylist = $this->createStylist('Sasa');
        $selectedStylist = $this->createStylist('Lina');

        $shortSlot = Slot::create([
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'kapster' => 'Sasa',
            'stylist_id' => $shortSlotStylist->id,
            'kuota' => 1,
            'terisi' => 0,
            'status' => 'tersedia',
        ]);

        $longSlot = Slot::create([
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '12:00:00',
            'jam_selesai' => '15:00:00',
            'kapster' => 'Lina',
            'stylist_id' => $selectedStylist->id,
            'kuota' => 1,
            'terisi' => 0,
            'status' => 'tersedia',
        ]);

        $serviceOne = Service::factory()->create([
            'nama_layanan' => 'Potong Rambut',
            'harga' => 100000,
            'durasi_menit' => 90,
            'status' => 'aktif',
        ]);

        $serviceTwo = Service::factory()->create([
            'nama_layanan' => 'Coloring',
            'harga' => 200000,
            'durasi_menit' => 90,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get('/booking?service_ids[]=' . $serviceOne->id . '&service_ids[]=' . $serviceTwo->id . '&stylist_id=' . $selectedStylist->id);

        $response->assertOk();
        $response->assertSee('12:00 - 15:00');
        $response->assertDontSee('10:00 - 11:00');
        $response->assertSee('name="slot_id" value="' . $longSlot->id . '" class="peer sr-only" required', false);
        $response->assertDontSee('name="slot_id" value="' . $shortSlot->id . '" class="peer sr-only" required', false);
    }

    public function test_user_can_book_multiple_services_in_one_booking(): void
    {
        $user = User::factory()->create();
        $stylist = $this->createStylist('Sasa');

        $slot = Slot::create([
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '14:00:00',
            'kapster' => 'Sasa',
            'stylist_id' => $stylist->id,
            'kuota' => 1,
            'terisi' => 0,
            'status' => 'tersedia',
        ]);

        $serviceOne = Service::factory()->create([
            'nama_layanan' => 'Potong Rambut',
            'harga' => 100000,
            'durasi_menit' => 60,
            'status' => 'aktif',
        ]);

        $serviceTwo = Service::factory()->create([
            'nama_layanan' => 'Coloring',
            'harga' => 200000,
            'durasi_menit' => 90,
            'status' => 'aktif',
        ]);

        $serviceThree = Service::factory()->create([
            'nama_layanan' => 'Treatment',
            'harga' => 150000,
            'durasi_menit' => 60,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->post('/booking', [
            'service_ids' => [$serviceOne->id, $serviceTwo->id, $serviceThree->id],
            'stylist_id' => $stylist->id,
            'slot_id' => $slot->id,
            'catatan' => 'Mau setengah styling',
        ]);

        $response->assertRedirect();

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals(450000, (int) $booking->total_harga);
        $this->assertEquals($slot->id, $booking->slot_id);
        $this->assertDatabaseHas('booking_service', [
            'booking_id' => $booking->id,
            'service_id' => $serviceOne->id,
        ]);
        $this->assertDatabaseHas('booking_service', [
            'booking_id' => $booking->id,
            'service_id' => $serviceTwo->id,
        ]);
        $this->assertDatabaseHas('booking_service', [
            'booking_id' => $booking->id,
            'service_id' => $serviceThree->id,
        ]);
    }

    public function test_booking_is_rejected_when_consecutive_hourly_slots_are_not_available(): void
    {
        $user = User::factory()->create();
        $stylist = $this->createStylist('Sasa');

        $morningSlot = Slot::create([
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'kapster' => 'Sasa',
            'stylist_id' => $stylist->id,
            'kuota' => 1,
            'terisi' => 0,
            'status' => 'tersedia',
        ]);

        $afternoonSlot = Slot::create([
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '12:00:00',
            'jam_selesai' => '13:00:00',
            'kapster' => 'Sasa',
            'stylist_id' => $stylist->id,
            'kuota' => 1,
            'terisi' => 0,
            'status' => 'tersedia',
        ]);

        $service = Service::factory()->create([
            'durasi_menit' => 120,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->post('/booking', [
            'service_ids' => [$service->id],
            'stylist_id' => $stylist->id,
            'slot_id' => $morningSlot->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_one_payment_proof_and_admin_approval_apply_to_the_whole_transaction(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $stylist = $this->createStylist('Sasa');

        $slots = collect(['10:00:00', '11:00:00'])->map(function ($startTime, $index) use ($stylist) {
            return Slot::create([
                'tanggal' => now()->addDay()->toDateString(),
                'jam_mulai' => $startTime,
            'jam_selesai' => $index === 0 ? '11:00:00' : '12:00:00',
            'kapster' => 'Sasa',
                'stylist_id' => $stylist->id,
                'kuota' => 1,
                'terisi' => 0,
                'status' => 'tersedia',
            ]);
        });

        $service = Service::factory()->create([
            'harga' => 100000,
            'durasi_menit' => 120,
            'status' => 'aktif',
        ]);

        $this->actingAs($user)->post('/booking', [
            'service_ids' => [$service->id],
            'stylist_id' => $stylist->id,
            'slot_id' => $slots->first()->id,
        ]);

        $bookings = Booking::orderBy('id')->get();
        $bookingIds = $bookings->pluck('id');
        $this->assertCount(1, $bookings);
        $this->assertSame(2, $bookings->first()->reservedSlots()->count());

        $this->actingAs($user)->get(route('booking.show', $bookings->first()))
            ->assertOk()
            ->assertSee('Total DP (20%)')
            ->assertSee('satu bukti pembayarannya');

        $this->actingAs($user)->post(route('booking.payment', $bookings->first()), [
            'bukti_pembayaran' => UploadedFile::fake()->create('transfer.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('booking.show', $bookings->first()));

        $bookings = Booking::whereIn('id', $bookingIds)->get();
        $this->assertSame(1, $bookings->pluck('bukti_transfer')->unique()->count());
        $this->assertSame(1, $bookings->pluck('status')->unique()->count());
        $this->assertSame('menunggu_pembayaran', $bookings->first()->status);
        $this->assertSame(1, \App\Models\Payment::whereIn('booking_id', $bookingIds)->where('status_pembayaran', 'pending')->count());

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.bookings.approve', $bookings->first()))
            ->assertSessionHas('success');

        $this->assertSame(1, Booking::whereIn('id', $bookingIds)->where('status', 'dibayar')->count());
        $this->assertSame(1, \App\Models\Payment::whereIn('booking_id', $bookingIds)->where('status_pembayaran', 'terverifikasi')->count());
    }

    public function test_one_booking_reserves_consecutive_hourly_slots_for_all_selected_services(): void
    {
        $user = User::factory()->create();
        $stylist = $this->createStylist('Sasa');
        $slots = collect(['10:00:00', '11:00:00', '12:00:00', '13:00:00'])->map(function ($startTime) use ($stylist) {
            return Slot::create([
                'tanggal' => now()->addDay()->toDateString(),
                'jam_mulai' => $startTime,
                'jam_selesai' => \Carbon\Carbon::parse($startTime)->addHour()->format('H:i:s'),
                'kapster' => 'Sasa',
                'stylist_id' => $stylist->id,
                'kuota' => 1,
                'terisi' => 0,
                'status' => 'tersedia',
            ]);
        });

        $services = collect([
            ['nama_layanan' => 'Haircut', 'harga' => 100000, 'durasi_menit' => 60],
            ['nama_layanan' => 'Coloring', 'harga' => 200000, 'durasi_menit' => 120],
            ['nama_layanan' => 'Hair Treatment', 'harga' => 150000, 'durasi_menit' => 60],
        ])->map(fn ($attributes) => Service::factory()->create($attributes + ['status' => 'aktif']));

        $response = $this->actingAs($user)->post('/booking', [
            'service_ids' => $services->pluck('id')->all(),
            'stylist_id' => $stylist->id,
            'slot_id' => $slots->first()->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('bookings', 1);

        $booking = Booking::with('reservedSlots')->firstOrFail();
        $this->assertSame(240, (int) $booking->durasi_total_menit);
        $this->assertSame(450000, (int) $booking->total_harga);
        $this->assertSame(4, $booking->reservedSlots->count());
        $this->assertEqualsCanonicalizing($slots->pluck('id')->all(), $booking->reservedSlots->pluck('id')->all());
    }

    public function test_booking_one_stylist_does_not_block_another_stylist_at_the_same_time(): void
    {
        $stylistA = $this->createStylist('Stylist A');
        $stylistB = $this->createStylist('Stylist B');
        $slotsByStylist = [];

        foreach ([$stylistA, $stylistB] as $stylist) {
            $slotsByStylist[$stylist->id] = collect(['10:00:00', '11:00:00'])->map(function ($startTime) use ($stylist) {
                return Slot::create([
                    'tanggal' => now()->addDay()->toDateString(),
                    'jam_mulai' => $startTime,
                    'jam_selesai' => \Carbon\Carbon::parse($startTime)->addHour()->format('H:i:s'),
                    'kapster' => $stylist->nama,
                    'stylist_id' => $stylist->id,
                    'kuota' => 1,
                    'terisi' => 0,
                    'status' => 'tersedia',
                ]);
            });
        }

        $service = Service::factory()->create([
            'durasi_menit' => 120,
            'status' => 'aktif',
        ]);
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $this->actingAs($userA)->post('/booking', [
            'service_ids' => [$service->id],
            'stylist_id' => $stylistA->id,
            'slot_id' => $slotsByStylist[$stylistA->id]->first()->id,
        ])->assertRedirect();

        $this->assertSame(2, Slot::where('stylist_id', $stylistA->id)->where('status', 'penuh')->count());
        $this->assertSame(0, Slot::where('stylist_id', $stylistB->id)->where('terisi', '>', 0)->count());

        $secondUser = User::factory()->create();
        $this->actingAs($secondUser)->post('/booking', [
            'service_ids' => [$service->id],
            'stylist_id' => $stylistA->id,
            'slot_id' => $slotsByStylist[$stylistA->id]->first()->id,
        ])->assertSessionHas('error');

        $this->actingAs($userB)->post('/booking', [
            'service_ids' => [$service->id],
            'stylist_id' => $stylistB->id,
            'slot_id' => $slotsByStylist[$stylistB->id]->first()->id,
        ])->assertRedirect();

        $this->assertSame(2, Booking::count());
        $this->assertDatabaseHas('bookings', ['stylist_id' => $stylistA->id]);
        $this->assertDatabaseHas('bookings', ['stylist_id' => $stylistB->id]);
    }

    private function createStylist(string $name): Stylist
    {
        return Stylist::create(['nama' => $name, 'status' => 'aktif']);
    }
}
