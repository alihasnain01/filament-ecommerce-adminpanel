<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Mail;
use App\Models\AppSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'App Settings';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount()
    {
        $data = AppSettings::first() ? AppSettings::first()->toArray() : [];
        $this->data = $data;
        $this->form->fill($this->data);
    }

    public function save()
    {
        try {
            $data = $this->form->getState();
            AppSettings::updateOrCreate(
                ['id' => 1],
                $data
            );

            Notification::make()
                ->success()
                ->title('Settings saved!')
                ->send();

            $activity = new Activity();
            $activity->log_name = 'App Settings';
            $activity->description = 'App Settings saved';
            $activity->event = 'Updated';
            $activity->causer_id = Auth::id();
            $activity->causer_type = Auth::user()->getMorphClass();
            $activity->properties = $data;
            $activity->save();
        } catch (Halt $exception) {
            return;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic info')
                    ->schema([
                        TextInput::make('app_name')
                            ->label('Name')
                            ->placeholder('Name'),
                        TextInput::make('app_url')
                            ->label('URL')
                            ->placeholder('URL'),
                        TextInput::make('app_description')
                            ->label('Description')
                            ->placeholder('Description'),
                        TextInput::make('app_keywords')
                            ->label('Keywords')
                            ->placeholder('Keywords'),
                    ])->columns(2),

                Section::make('Contact info')
                    ->schema([
                        TextInput::make('app_email')
                            ->label('Email')
                            ->placeholder('Email'),
                        TextInput::make('app_phone')
                            ->label('Phone')
                            ->placeholder('Phone')
                            ->tel()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                        TextInput::make('app_address')
                            ->label('Address')
                            ->placeholder('Address'),
                        TextInput::make('google_map_link')
                            ->label('Google Map Link')
                            ->placeholder('Map Link'),
                    ])->columns(2),

                Section::make('Social')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->placeholder('Facebook URL'),
                        TextInput::make('twitter_url')
                            ->label('Twitter')
                            ->placeholder('Twitter URL'),
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->placeholder('Instagram URL'),
                        TextInput::make('youtube_url')
                            ->label('Youtube')
                            ->placeholder('Youtube URL'),
                        TextInput::make('linkedin_url')
                            ->label('Linkedin')
                            ->placeholder('Linkedin URL'),
                        TextInput::make('telegram_num')
                            ->label('Telegram')
                            ->placeholder('Telegram number'),
                    ])->columns(2)
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
                ->url(filament()->getUrl())
                ->color('gray'),
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action('save')
        ];
    }

    // protected function getFormActions(): array
    // {
    //     return [
    //         Action::make('back')
    //             ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
    //             ->url(filament()->getUrl())
    //             ->color('gray'),
    //         Action::make('save')
    //             ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
    //             ->submit('save'),
    //     ];
    // }
}
