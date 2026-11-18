<?php

namespace App\Filament\Pages;

use App\Models\Settings as ModelsSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class Settings extends Page
{

    use HasPageShield;

    public static function getNavigationGroup(): string
    {
        return __('resource.navigationGroup.system');
    }

    public  static function getNavigationLabel(): string
    {
        return __('resource.setting.navigationLabel');
    }


    public function getTitle(): string
    {
        return __('resource.setting.pluralModelLabel');
    }

    //  protected static string $routePath = 'analytics'; // Tip mütləq "string" olmalıdır (?string YOX!)
    //   protected static ?string $slug = 'analytics';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(ModelsSettings::current()->toArray());
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $setting = ModelsSettings::current();
            $oldLogo = $setting->logo;

            $setting->update($data);

            if (filled($oldLogo) && $oldLogo !== $setting->logo) {
                Storage::disk('public')->delete($oldLogo);
            }

            Notification::make()
                ->title(__('resource.save_success'))
                ->success()
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make(__('resource.storeInfo'))
                    ->schema([
                        FileUpload::make('logo')
                            ->label(__('resource.brand.logo'))
                            ->image()
                            ->disk('public')
                            ->directory('settings')
                            ->maxSize(4096)
                            ->imageEditor()
                            ->openable()
                            ->deletable(true)
                            ->placeholder(__('resource.noImage'))
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),

                        TextInput::make('app_name')
                            ->label(__('resource.app_name'))
                            ->maxLength(80),

                        TextInput::make('name_store')
                            ->label(__('resource.name_store'))
                            ->maxLength(80),

                        TextInput::make('vat')
                            ->label(__('resource.vat'))
                            ->maxLength(20),

                        TextInput::make('phone')
                            ->label(__('resource.phone'))
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('address')
                            ->label(__('resource.address'))
                            ->maxLength(100),

                        TextInput::make('website')
                            ->label(__('resource.website'))
                            ->maxLength(80),

                        TextInput::make('support_email')
                            ->label(__('resource.support_email'))
                            ->email()
                            ->maxLength(80),

                    ]),


                Section::make(__('resource.receiptInfo'))
                    ->schema([
                        MarkdownEditor::make('receipt_header')
                            ->label(__('resource.receipt_header'))
                            ->maxLength(250),

                        MarkdownEditor::make('receipt_footer')
                            ->label(__('resource.receipt_footer'))
                            ->maxLength(250),

                        Toggle::make('receipt_show_logo')->label(__('resource.receipt_show_logo')),
                        Toggle::make('receipt_show_phone')->label(__('resource.receipt_show_phone')),
                        Toggle::make('receipt_show_address')->label(__('resource.receipt_show_address')),
                        Toggle::make('receipt_show_vat')->label(__('resource.receipt_show_vat')),

                        Section::make(__('resource.taxInfo'))
                            ->schema([
                                TextInput::make('currency_code')->label(__('resource.currency_code'))->maxLength(5),
                                TextInput::make('currency_symbol')->label(__('resource.currency_symbol'))->maxLength(5),
                                Toggle::make('tax_enabled')
                                    ->label(__('resource.tax_enabled'))
                                    ->default(false),
                                TextInput::make('tax_rate')
                                    ->label(__('resource.tax_rate'))
                                    ->maxLength(20)
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->default(0),
                            ])->columns(2),

                    ]),

            ])->columns(2);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('resource.save'))
                ->action('save'),
        ];
    }




    protected string $view = 'filament.pages.settings';
}
