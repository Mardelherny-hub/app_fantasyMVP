<?php

namespace App\Livewire\Admin\Market;

use Livewire\Component;
use App\Models\League;
use App\Models\MarketSettings;

class MarketSettingsForm extends Component
{
    public $selectedLeagueId;
    public $max_multiplier = 3.00;
    public $trade_window_open = true;
    public $loan_allowed = false;
    public $min_offer_cooldown_h = 2;

    public function mount()
    {
        $firstLeague = League::with('marketSettings')->first();
        if ($firstLeague) {
            $this->selectedLeagueId = $firstLeague->id;
            $this->loadSettings();
        }
    }

    public function updatedSelectedLeagueId()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $league = League::with('marketSettings')->find($this->selectedLeagueId);
        
        if ($league && $league->marketSettings) {
            $settings = $league->marketSettings;
            $this->max_multiplier = $settings->max_multiplier;
            $this->trade_window_open = $settings->trade_window_open;
            $this->loan_allowed = $settings->loan_allowed;
            $this->min_offer_cooldown_h = $settings->min_offer_cooldown_h;
        } else {
            $this->max_multiplier = 3.00;
            $this->trade_window_open = true;
            $this->loan_allowed = false;
            $this->min_offer_cooldown_h = 2;
        }
    }

    public function save()
    {
        $this->validate([
            'max_multiplier' => 'required|numeric|min:1|max:10',
            'trade_window_open' => 'required|boolean',
            'loan_allowed' => 'required|boolean',
            'min_offer_cooldown_h' => 'required|integer|min:1|max:48',
        ]);

        $league = League::findOrFail($this->selectedLeagueId);
        
        $settings = $league->marketSettings ?? new MarketSettings(['league_id' => $league->id]);
        $settings->max_multiplier = $this->max_multiplier;
        $settings->trade_window_open = $this->trade_window_open;
        $settings->loan_allowed = $this->loan_allowed;
        $settings->min_offer_cooldown_h = $this->min_offer_cooldown_h;
        $settings->save();

        session()->flash('success', __('Settings saved successfully.'));
        $this->dispatch('settingsSaved');
    }

    public function render()
    {
        $leagues = League::select('id', 'name')->get();
        return view('livewire.admin.market.market-settings-form', compact('leagues'));
    }
}