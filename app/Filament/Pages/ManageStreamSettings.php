<?php

namespace App\Filament\Pages;

use App\Models\StreamSettings;
use Filament\Pages\Page;
use Filament\Schemas\Schema; // The Type the method wants
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput; 
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use BackedEnum;
use Filament\Actions\Action;

class ManageStreamSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;
    protected string $view = 'filament.pages.manage-stream-settings';
    protected static ?string $title = 'Stream Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $record = StreamSettings::first() ?? new StreamSettings([
            'rtmp_url' => 'rtmp://' . request()->getHost() . '/live',
            'stream_key' => StreamSettings::generateNewKey(),
        ]);
        
        $this->form->fill($record->toArray());
    }

    /**
     * We type-hint Schema, but use the Form Components inside.
     * In this version of Filament, they are cross-compatible.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('RTMP Server Configuration')
                    ->description('These settings connect your encoder (OBS) to the server.')
                    ->schema([
                        TextInput::make('rtmp_url')
                            ->label('RTMP URL')
                            ->required()
                            ->copyable(),
                        
                        TextInput::make('stream_key')
                            ->label('Stream Key')
                            ->password()
                            ->revealable()
                            ->readonly()
                            ->required()
                            ->hintAction(
                                Action::make('regenerate')
                                    ->icon('heroicon-m-arrow-path')
                                    ->color('warning')
                                    ->requiresConfirmation()
                                    ->action(function () {
                                        $newKey = StreamSettings::generateNewKey();
                                        $this->data['stream_key'] = $newKey;
                                        Notification::make()
                                            ->title('New key generated. Remember to save!')
                                            ->warning()
                                            ->send();
                                    })
                            ),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $record = StreamSettings::first() ?? new StreamSettings();
            
            $record->fill($data);
            $record->updated_by = auth()->id();
            $record->save();

            Notification::make()
                ->title('Settings saved successfully')
                ->success()
                ->send();
                
        } catch (Halt $exception) {
            return;
        }
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Settings')
                ->color('primary')
                ->submit('save'),
        ];
    }
}