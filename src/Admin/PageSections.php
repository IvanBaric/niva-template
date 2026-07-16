<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Admin;

use IvanBaric\NivaTemplate\Admin\Options\BlogTaxonomyFieldOptions;
use IvanBaric\NivaTemplate\Admin\Options\GalleryFieldOptions;
use IvanBaric\NivaTemplate\Admin\Options\ProductTaxonomyFieldOptions;
use IvanBaric\Pages\Admin\Action;
use IvanBaric\Pages\Admin\AdminSection;
use IvanBaric\Pages\Admin\Field;
use IvanBaric\Pages\Admin\LayoutVariant;
use IvanBaric\Pages\Admin\Tab;

final class PageSections
{
    /** @return array<int, AdminSection> */
    public function definitions(): array
    {
        $contentBlocksDescription = __('Uredite blokove s naslovom, opisom, slikom i ikonom. Koristite ih za vrijednosti, aktivnosti, misiju i viziju, tim, prednosti, radionice, projekte, procese, programe ili druge kratke sadržajne cjeline.');

        return [
            $this->templateHeader(),
            $this->templateFooter(),
            $this->templateSections(),
            $this->hero(),
            $this->featuredValues()
                ->label(__('Sadržajni blokovi'))
                ->option('creator_hidden', true),
            $this->contentBlocks(),
            $this->about(),
            $this->collaboration(),
            $this->testimonials(),
            $this->faq(),
            $this->features()
                ->label(__('Sadržajni blokovi'))
                ->option('creator_hidden', true),
            $this->partners(),
            $this->statistics(),
            $this->productListing('featured_products', __('Istaknuti radovi'), 6)
                ->option('creator_hidden', true),
            $this->productListing('all_products', __('Radovi'), 12),
            $this->galleryListing('gallery')
                ->option('creator_hidden', true),
            $this->galleryListing('gallery_grid'),
            $this->photoGallery()
                ->option('creator_hidden', true),
            $this->video(),
            $this->calendar(),
            $this->newsListing('featured_news', __('Istaknute objave'), 3)
                ->option('creator_hidden', true),
            $this->newsListing('latest_news', __('Objave'), 12),
            $this->newsListing('taxonomy_news', __('Objave prema taxonomy'), 6)
                ->option('creator_hidden', true),
            $this->story('mission', __('Sadržajni blokovi'), $contentBlocksDescription)
                ->option('creator_hidden', true),
            $this->story('vision', __('Sadržajni blokovi'), $contentBlocksDescription)
                ->option('creator_hidden', true),
            $this->story('values', __('Sadržajni blokovi'), $contentBlocksDescription)
                ->option('creator_hidden', true),
            $this->story('team', __('Sadržajni blokovi'), $contentBlocksDescription)
                ->option('creator_hidden', true),
            $this->howToOrder(),
            $this->contact(),
            $this->socialLinks(),
        ];
    }

    private function templateSections(): AdminSection
    {
        return AdminSection::add('template_sections')
            ->label(__('Sekcije'))
            ->option('storage', 'settings.templates.niva-classic.sections')
            ->messages([
                'layout_saved' => __('Izgled sekcija je spremljen.'),
            ])
            ->tabs([
                Tab::layout(__('Izgled'))
                    ->heading(__('Naslovi sekcija'))
                    ->description(__('Odaberite poravnanje i blagi naglasak za nazive i opise sekcija na javnoj stranici.'))
                    ->storage('section_header_variant')
                    ->default('left')
                    ->submitLabel(__('Spremi izgled'))
                    ->variants([
                        LayoutVariant::add('left')
                            ->label(__('Lijevo'))
                            ->description(__('Trenutni mirni prikaz s nazivom i opisom poravnatim lijevo.'))
                            ->preview('section_header_left'),

                        LayoutVariant::add('left_accent')
                            ->label(__('Lijevo s naglaskom'))
                            ->description(__('Lijevo poravnanje s jačim naslovom, istaknutim nadnaslovom i čvršćim opisom.'))
                            ->preview('section_header_left_accent'),

                        LayoutVariant::add('left_colored')
                            ->label(__('Lijevo obojano'))
                            ->description(__('Lijevo poravnanje sa zadnjim dijelom naslova u primarnoj boji.'))
                            ->preview('section_header_left_colored'),

                        LayoutVariant::add('center')
                            ->label(__('Sredina'))
                            ->description(__('Naziv i opis sekcije poravnati su u sredinu.'))
                            ->preview('section_header_center'),

                        LayoutVariant::add('center_accent')
                            ->label(__('Sredina s naglaskom'))
                            ->description(__('Središnje poravnanje s jačim naslovom, istaknutim nadnaslovom i čvršćim opisom.'))
                            ->preview('section_header_center_accent'),

                        LayoutVariant::add('center_colored')
                            ->label(__('Sredina obojano'))
                            ->description(__('Središnje poravnanje sa zadnjim dijelom naslova u primarnoj boji.'))
                            ->preview('section_header_center_colored'),

                        LayoutVariant::add('marker')
                            ->label(__('Marker'))
                            ->description(__('Središnji naslov sa zadnjim dijelom istaknutim blagom obojanom pozadinom.'))
                            ->preview('section_header_marker'),

                        LayoutVariant::add('split')
                            ->label(__('Podijeljeno'))
                            ->description(__('Na većim ekranima naslov ostaje lijevo, a opis se premješta desno.'))
                            ->preview('section_header_split'),

                        LayoutVariant::add('split_accent')
                            ->label(__('Podijeljeno s naglaskom'))
                            ->description(__('Podijeljeni raspored s jačim naslovom i mirnim naglaskom.'))
                            ->preview('section_header_split_accent'),

                        LayoutVariant::add('split_colored')
                            ->label(__('Podijeljeno obojano'))
                            ->description(__('Podijeljeni raspored sa zadnjim dijelom naslova u primarnoj boji.'))
                            ->preview('section_header_split_colored'),

                        LayoutVariant::add('side_label')
                            ->label(__('Bočna crta'))
                            ->description(__('Tanka crta lijevo od naslova daje sekciji jači, ali miran uvodni naglasak.'))
                            ->preview('section_header_side_label'),
                    ]),
            ]);
    }

    private function templateFooter(): AdminSection
    {
        return AdminSection::add('template_footer')
            ->label(__('Podnožje'))
            ->option('storage', 'settings.templates.niva-classic.footer')
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Podnožje je spremljeno.'),
                'layout_saved' => __('Izgled podnožja je spremljen.'),
            ])
            ->tabs([
                Tab::form(__('Sadržaj'))
                    ->heading(__('Sadržaj'))
                    ->description(__('Uredite tekst koji se prikazuje u dnu javne stranice.'))
                    ->submitLabel(__('Spremi podnožje'))
                    ->fields([
                        Field::textarea('description')
                            ->label(__('Opis podnožja'))
                            ->rows(3)
                            ->nullable()
                            ->max(300)
                            ->defaultFrom('description')
                            ->help(__('Kratki tekst koji se prikazuje ispod naziva zadruge u podnožju.')),

                        Field::text('copyright')
                            ->label(__('Tekst podnožja'))
                            ->required()
                            ->max(180)
                            ->default(__('© :year Školska zadruga. Sva prava pridržana.', ['year' => now()->year])),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled'))
                    ->description(__('Trenutno je dostupan jedan usklađeni izgled podnožja.'))
                    ->storage('layout_variant')
                    ->default('classic')
                    ->submitLabel(__('Spremi izgled'))
                    ->variants([
                        LayoutVariant::add('classic')
                            ->label(__('Klasično podnožje'))
                            ->description(__('Postojeći izgled s nazivom zadruge, poveznicama i tekstom autorskih prava.'))
                            ->preview('footer_classic'),
                    ]),
            ]);
    }

    private function templateHeader(): AdminSection
    {
        return AdminSection::add('template_header')
            ->label(__('Zaglavlje'))
            ->option('storage', 'settings.templates.niva-classic.header')
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Zaglavlje je spremljeno.'),
                'layout_saved' => __('Izgled zaglavlja je spremljen.'),
            ])
            ->tabs([
                Tab::form(__('Sadržaj'))
                    ->heading(__('Sadržaj'))
                    ->description(__('Uredite sadržaj i odaberite jedan usklađeni izgled javnog zaglavlja.'))
                    ->submitLabel(__('Spremi zaglavlje'))
                    ->fields([
                        Field::text('eyebrow')
                            ->label(__('Nadnaslov'))
                            ->nullable()
                            ->max(120)
                            ->default(__('Klasična udruga')),

                        Field::text('title')
                            ->label(__('Naslov'))
                            ->required()
                            ->max(160)
                            ->defaultFrom('name'),

                        Field::textarea('subtitle')
                            ->label(__('Podnaslov'))
                            ->rows(4)
                            ->nullable()
                            ->max(500)
                            ->defaultFrom('description'),

                        Field::image('logo')
                            ->label(__('Logo'))
                            ->help(corexis_image_upload()->helpText().' '.__('Logo se prikazuje u zaglavlju, podnožju i kao oznaka javne stranice.'))
                            ->size('w-full aspect-[16/9]')
                            ->fit('contain')
                            ->mediaCollection((string) config('niva-template.media.organization_logo_collection', 'website_logo'))
                            ->storeAsGalleryMedia()
                            ->option('media_conversion', 'medium')
                            ->option('media_title', __('Logo organizacije'))
                            ->option('store_value', false),

                        Field::image('image')
                            ->label(__('Slika zaglavlja'))
                            ->help(corexis_image_upload()->helpText().' '.__('Slika će se prilagoditi odabranom izgledu zaglavlja.'))
                            ->size('w-full aspect-[16/9]')
                            ->mediaCollection((string) config('niva-template.media.organization_header_collection', 'website_header_image'))
                            ->storeAsGalleryMedia()
                            ->option('media_conversion', 'hero')
                            ->option('media_title', __('Slika zaglavlja'))
                            ->option('store_value', false),

                        Field::image('mobile_image')
                            ->label(__('Mobilna slika zaglavlja'))
                            ->help(corexis_image_upload()->helpText().' '.__('Koristite vertikalnu sliku za mobilni prikaz zaglavlja. Ako nije unesena, koristi se glavna slika zaglavlja.'))
                            ->size('w-full aspect-[3/4]')
                            ->mediaCollection((string) config('niva-template.media.organization_mobile_header_collection', 'website_mobile_header_image'))
                            ->storeAsGalleryMedia()
                            ->option('media_conversion', 'hero')
                            ->option('media_title', __('Mobilna slika zaglavlja'))
                            ->option('store_value', false),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled'))
                    ->description(__('Odaberite jedan usklađeni izgled javnog zaglavlja.'))
                    ->storage('header_variant')
                    ->default('header-1')
                    ->submitLabel(__('Spremi izgled'))
                    ->variants([
                        LayoutVariant::add('header-1')
                            ->label(__('Zaglavlje 1'))
                            ->description(__('Velika fotografija, tamna navigacija i tekst u sigurnom kontrastnom panelu.'))
                            ->preview('header_hero'),

                        LayoutVariant::add('header-2')
                            ->label(__('Zaglavlje 2'))
                            ->description(__('Centralno zaglavlje preko fotografije s tamnim slojem za snažan kontrast.'))
                            ->preview('header_editorial'),

                        LayoutVariant::add('header-3')
                            ->label(__('Zaglavlje 3'))
                            ->description(__('Kompaktnije kreativno zaglavlje sa sticky izbornikom i tekstom desno.'))
                            ->preview('header_sticky'),

                        LayoutVariant::add('header-4')
                            ->label(__('Zaglavlje 4'))
                            ->description(__('Bijeli uvodni blok s plutajućom navigacijom, većim logom i dijagonalnom fotografijom desno.'))
                            ->preview('header_split'),

                        LayoutVariant::add('header-5')
                            ->label(__('Zaglavlje 5'))
                            ->description(__('Prostrano zaglavlje s toplom lijevom zonom i fotografijom koja mekano prelazi u pozadinu.'))
                            ->preview('header_craft'),

                        LayoutVariant::add('header-6')
                            ->label(__('Zaglavlje 6'))
                            ->description(__('Snažno podijeljeno zaglavlje s primary bojom lijevo i velikom fotografijom desno.'))
                            ->preview('header_primary_split'),

                        LayoutVariant::add('header-7')
                            ->label(__('Zaglavlje 7'))
                            ->description(__('Bijela navigacija iznad velike fotografije s tamnim uvodnim slojem i istaknutim pozivima na akciju.'))
                            ->preview('header_showcase'),

                        LayoutVariant::add('header-8')
                            ->label(__('Zaglavlje 8'))
                            ->description(__('Prostrana fotografija u urednom okviru s naslovom i pozivima na akciju u mirnoj zoni ispod nje.'))
                            ->preview('header_gallery_frame'),
                    ]),

                Tab::settings(__('Postavke'))
                    ->heading(__('Postavke'))
                    ->description(__('Uredite prikaz logotipa i gumbe koji se prikazuju ispod uvodnog teksta na naslovnici. Za svaki gumb možete uključiti prikaz, upisati tekst i odabrati aktivnu stranicu na koju vodi.'))
                    ->fields([
                        Field::boolean('show_logo')
                            ->label(__('Prikaži logo'))
                            ->help(__('Uključite prikaz logotipa u javnom zaglavlju. Ako je isključeno, logo se neće prikazati ni u navigaciji ni u hero sadržaju.'))
                            ->default(true)
                            ->storage('show_logo'),

                        Field::boolean('cta_1_enabled')
                            ->label(__('Prikaži glavni gumb'))
                            ->help(__('Glavni gumb je istaknut primarnom bojom i najbolje ga je koristiti za najvažniju stranicu, primjerice radove zadruge.'))
                            ->default(true)
                            ->storage('cta.1.enabled'),

                        Field::text('cta_1_text')
                            ->label(__('Tekst glavnog gumba'))
                            ->help(__('Neka tekst bude kratak i jasan, npr. Pogledaj radove.'))
                            ->nullable()
                            ->max(80)
                            ->default(__('Pogledaj radove'))
                            ->storage('cta.1.text'),

                        Field::select('cta_1_page_uuid')
                            ->label(__('Odredište glavnog gumba'))
                            ->help(__('Odaberite aktivnu stranicu na koju korisnik dolazi klikom na glavni gumb.'))
                            ->rules(['nullable', 'string'])
                            ->option('options_source', 'published_pages')
                            ->option('default_page_key', 'products')
                            ->storage('cta.1.page_uuid'),

                        Field::boolean('cta_2_enabled')
                            ->label(__('Prikaži dodatni gumb'))
                            ->help(__('Dodatni gumb je mirniji i dobar je za informativnu stranicu, primjerice predstavljanje zadruge.'))
                            ->default(true)
                            ->storage('cta.2.enabled'),

                        Field::text('cta_2_text')
                            ->label(__('Tekst dodatnog gumba'))
                            ->help(__('Upišite kratak tekst dodatne poveznice, npr. O zadruzi.'))
                            ->nullable()
                            ->max(80)
                            ->default(__('O zadruzi'))
                            ->storage('cta.2.text'),

                        Field::select('cta_2_page_uuid')
                            ->label(__('Odredište dodatnog gumba'))
                            ->help(__('Odaberite aktivnu stranicu na koju korisnik dolazi klikom na dodatni gumb.'))
                            ->rules(['nullable', 'string'])
                            ->option('options_source', 'published_pages')
                            ->option('default_page_key', 'about')
                            ->storage('cta.2.page_uuid'),
                    ]),
            ]);
    }

    private function hero(): AdminSection
    {
        return AdminSection::add('hero')
            ->label(__('Uvodni blok'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Uvodni blok je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Uvodni blok'))
                    ->description(__('Uredite glavni uvodni sadržaj koji se prikazuje na početku stranice.'))
                    ->formTitle(__('Uredi uvodni blok'), __('Dodaj uvodni blok'))
                    ->addLabel(__('Dodaj uvodni blok'))
                    ->emptyText(__('Nema dodanog uvodnog sadržaja.'))
                    ->modalDescription(__('Unesite naslov, tekst, sliku i poveznicu za uvodni blok.'))
                    ->showVisibility(false)
                    ->inlineForm(submitLabel: __('Spremi sadržaj'))
                    ->fields([
                        Field::text('title')
                            ->label(__('Naslov'))
                            ->required()
                            ->max(160),

                        Field::textarea('content')
                            ->label(__('Opis'))
                            ->rows(5)
                            ->nullable()
                            ->max(700),

                        Field::image('image')
                            ->label(__('Slika'))
                            ->help(corexis_image_upload()->helpText())
                            ->size('max-w-sm aspect-[4/3]')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),

                        Field::text('button_text')
                            ->label(__('Tekst gumba'))
                            ->nullable()
                            ->max(80),

                        Field::url('button_url')
                            ->label(__('URL gumba'))
                            ->nullable()
                            ->max(2048),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati uvodni blok?'))
                            ->confirmText(__('Uvodni sadržaj će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),
            ]);
    }

    private function about(): AdminSection
    {
        return $this->withDemo(AdminSection::add('about'), 'about')
            ->label(__('O zadruzi'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled sekcije je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('O zadruzi'))
                    ->description(__('Uredite tekst, istaknutu misao i sliku koji se prikazuju u ovoj sekciji.'))
                    ->formTitle(__('Uredi sadržaj'), __('Dodaj sadržaj'))
                    ->addLabel(__('Dodaj sadržaj'))
                    ->emptyText(__('Nema dodanog sadržaja.'))
                    ->modalDescription(__('Unesite tekst i sliku za sekciju O zadruzi.'))
                    ->showVisibility(false)
                    ->inlineForm(submitLabel: __('Spremi sadržaj'))
                    ->fields([
                        Field::textarea('content')
                            ->label(__('O zadruzi'))
                            ->rows(10)
                            ->nullable(),

                        Field::textarea('subtitle')
                            ->label(__('Istaknuta misao'))
                            ->help(__('Kratka rečenica koja daje premium naglasak ovoj sekciji. Ako ostane prazna, neće se prikazati.'))
                            ->rows(3)
                            ->nullable()
                            ->max(500),

                        Field::image('image')
                            ->label(__('Slika'))
                            ->help(corexis_image_upload()->helpText().' '.__('Najbolje izgleda horizontalna fotografija radionice, prostora ili članova udruge.'))
                            ->size('w-full aspect-[16/9]')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati sadržaj?'))
                            ->confirmText(__('Sadržaj će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite kako će se tekst i slika prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('split')
                    ->variants([
                        LayoutVariant::add('split')
                            ->label(__('Uravnoteženo'))
                            ->description(__('Siguran prikaz s tekstom i slikom u jasnom, mirnom odnosu.'))
                            ->option('preview', 'about_split'),

                        LayoutVariant::add('cover')
                            ->label(__('Foto uvod'))
                            ->description(__('Velika fotografija otvara sekciju, a tekst se nastavlja ispod nje.'))
                            ->option('preview', 'about_cover'),

                        LayoutVariant::add('background')
                            ->label(__('Fotografija kao pozadina'))
                            ->description(__('Slika preko cijele širine, s tekstom na kontrastnoj staklenoj kartici.'))
                            ->option('preview', 'about_background'),

                        LayoutVariant::add('diagonal')
                            ->label(__('Dijagonalni rez'))
                            ->description(__('Moderan raspored s dijagonalnim rezom slike i tekstom na prozirnoj kartici.'))
                            ->option('preview', 'about_diagonal'),

                        LayoutVariant::add('curved_image')
                            ->label(__('Dijagonalni rez slike'))
                            ->description(__('Velika slika lijevo ima blagi SVG dijagonalni rez na desnom rubu, a tekst ostaje desno.'))
                            ->option('preview', 'about_curved_image'),

                        LayoutVariant::add('letter')
                            ->label(__('Topla bilješka'))
                            ->description(__('Meki, osobniji prikaz koji djeluje kao priča o udruzi.'))
                            ->option('preview', 'about_letter'),

                        LayoutVariant::add('editorial_frame')
                            ->label(__('Urednički preklop'))
                            ->description(__('Velika fotografija i istaknuta misao grade slojevitu naslovničku kompoziciju.'))
                            ->option('preview', 'about_editorial_frame'),
                    ]),
            ]);
    }

    private function featuredValues(): AdminSection
    {
        return $this->withDemo(AdminSection::add('featured_values'), 'featured_values')
            ->label(__('Istaknute vrijednosti'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled sekcije je spremljen.'),
                'settings_saved' => __('Postavke sekcije su spremljene.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Istaknute vrijednosti'))
                    ->description(__('Kratki blok ispod zaglavlja za prikaz najvažnijih vrijednosti, prednosti ili poruka.'))
                    ->formTitle(__('Uredi vrijednost'), __('Dodaj vrijednost'))
                    ->addLabel(__('Dodaj vrijednost'))
                    ->emptyText(__('Nema dodanih vrijednosti.'))
                    ->modalDescription(__('Unesite naslov, opis i ikonu za ovu vrijednost.'))
                    ->modalFlyout()
                    ->singleColumn()
                    ->hideSortOrder()
                    ->fields([
                        Field::text('title')
                            ->label(__('Naslov'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Opis'))
                            ->rows(4)
                            ->nullable(),

                        Field::image('image')
                            ->label(__('Slika'))
                            ->help(corexis_image_upload()->helpText())
                            ->size('small')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),

                        Field::icon('icon')
                            ->label(__('Ikona'))
                            ->nullable()
                            ->max(255),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati vrijednost?'))
                            ->confirmText(__('Odabrana vrijednost će se premjestiti u arhivu. Možete je kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite koliko će se vrijednosti prikazati u jednom redu na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('columns_3')
                    ->variants([
                        LayoutVariant::add('columns_2')
                            ->label(__('2 u redu'))
                            ->description(__('Prostran prikaz s dvije vrijednosti u jednom redu.'))
                            ->option('preview', 'featured_values_columns_2'),

                        LayoutVariant::add('columns_3')
                            ->label(__('3 u redu'))
                            ->description(__('Uravnotežen info-strip s tri vrijednosti u jednom redu.'))
                            ->option('preview', 'featured_values_columns_3'),

                        LayoutVariant::add('columns_4')
                            ->label(__('4 u redu'))
                            ->description(__('Gušći prikaz za kraće vrijednosti i širi desktop.'))
                            ->option('preview', 'featured_values_columns_4'),
                    ]),
            ]);
    }

    private function contentBlocks(): AdminSection
    {
        return $this->withDemo(AdminSection::add('content_blocks'), 'content_blocks')
            ->label(__('Sadržajni blokovi'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled sekcije je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Sadržajni blokovi'))
                    ->description(__('Dodajte blokove s naslovom, opisom, slikom i ikonom. Ovu sekciju možete koristiti za vrijednosti, aktivnosti, misiju i viziju, tim, prednosti, radionice, projekte, procese, usluge, programe, način rada ili bilo koji niz kratkih sadržajnih cjelina.'))
                    ->formTitle(__('Uredi blok'), __('Dodaj blok'))
                    ->addLabel(__('Dodaj blok'))
                    ->emptyText(__('Nema dodanih blokova.'))
                    ->modalDescription(__('Unesite naslov, opis, sliku i ikonu za ovaj blok.'))
                    ->modalFlyout()
                    ->singleColumn()
                    ->hideSortOrder()
                    ->fields([
                        Field::text('title')
                            ->label(__('Naslov'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Opis'))
                            ->rows(7)
                            ->nullable(),

                        Field::image('image')
                            ->label(__('Slika'))
                            ->help(corexis_image_upload()->helpText())
                            ->size('small')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),

                        Field::icon('icon')
                            ->label(__('Ikona'))
                            ->nullable()
                            ->max(255),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati blok?'))
                            ->confirmText(__('Odabrani blok će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite kako će se isti blokovi prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('mosaic')
                    ->variants([
                        LayoutVariant::add('columns_2')
                            ->label(__('2 u redu'))
                            ->description(__('Prostran prikaz s dva bloka u jednom redu.'))
                            ->option('preview', 'featured_values_columns_2'),

                        LayoutVariant::add('columns_3')
                            ->label(__('3 u redu'))
                            ->description(__('Uravnotežen prikaz s tri bloka u jednom redu.'))
                            ->option('preview', 'featured_values_columns_3'),

                        LayoutVariant::add('columns_4')
                            ->label(__('4 u redu'))
                            ->description(__('Gušći prikaz za kraće blokove i širi desktop.'))
                            ->option('preview', 'featured_values_columns_4'),

                        LayoutVariant::add('mosaic')
                            ->label(__('Jednake foto kartice'))
                            ->description(__('Uredan prikaz u jednakim karticama sa slikom, naslovom i cijelim opisom.'))
                            ->option('preview', 'features_mosaic'),

                        LayoutVariant::add('photo_cards')
                            ->label(__('Fotografske kartice'))
                            ->description(__('Kartice s fotografijom, istaknutom ikonom i kraćim opisom.'))
                            ->option('preview', 'features_photo_cards'),

                        LayoutVariant::add('editorial')
                            ->label(__('Urednički niz'))
                            ->description(__('Široke fotografije s opisima u mirnom časopisnom ritmu.'))
                            ->option('preview', 'features_editorial'),

                        LayoutVariant::add('spotlight')
                            ->label(__('Istaknuti pregled'))
                            ->description(__('Prvi blok dobiva veći prostor, a ostali se nastavljaju u urednoj mreži.'))
                            ->option('preview', 'features_spotlight'),

                        LayoutVariant::add('alternating')
                            ->label(__('Naizmjenični redovi'))
                            ->description(__('Vodoravni redovi u kojima se fotografija i tekst izmjenjuju.'))
                            ->option('preview', 'features_alternating'),

                        LayoutVariant::add('path')
                            ->label(__('Put stvaranja'))
                            ->description(__('Naizmjenični prikaz slike i teksta koji djeluje kao mirna priča kroz sadržaj.'))
                            ->option('preview', 'features_path'),

                        LayoutVariant::add('story_path')
                            ->label(__('Put stvaranja - priča'))
                            ->description(__('Mirniji naizmjenični prikaz preuzet iz story blokova, dobar za misiju, vrijednosti ili tim.'))
                            ->option('preview', 'story_path'),

                        LayoutVariant::add('studio')
                            ->label(__('Studio ploča'))
                            ->description(__('Topliji prikaz s fotografijama i opisima kao pregled radioničkog dana.'))
                            ->option('preview', 'features_studio'),

                        LayoutVariant::add('media_right')
                            ->label(__('Tekst lijevo'))
                            ->description(__('Tekst je lijevo i poravnat po sredini fotografije koja stoji desno.'))
                            ->option('preview', 'story_media_right'),

                        LayoutVariant::add('media_left')
                            ->label(__('Slika lijevo'))
                            ->description(__('Fotografija je lijevo, a tekst desno u istom mirnom ritmu.'))
                            ->option('preview', 'story_media_left'),

                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Siguran i uredan prikaz koji dobro radi s različitim brojem blokova.'))
                            ->option('preview', 'story_cards'),

                        LayoutVariant::add('showcase')
                            ->label(__('Istaknuto'))
                            ->description(__('Prvi blok dobiva veći vizualni prostor, ostali ga prate u ritmu.'))
                            ->option('preview', 'story_showcase'),

                        LayoutVariant::add('journal')
                            ->label(__('Redovi'))
                            ->description(__('Mirniji prikaz s više prostora za tekst i opis svakog bloka.'))
                            ->option('preview', 'story_journal'),
                    ]),
            ]);
    }

    private function collaboration(): AdminSection
    {
        return $this->withDemo(AdminSection::add('collaboration'), 'collaboration')
            ->label(__('Uključite se'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled sekcije je spremljen.'),
                'settings_saved' => __('Postavke sekcije su spremljene.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Uključite se'))
                    ->description(__('Uredite jedan istaknuti poziv s tekstom i slikom. Tekst i poveznica gumba uređuju se u postavkama sekcije.'))
                    ->formTitle(__('Uredi poziv'), __('Dodaj poziv'))
                    ->addLabel(__('Dodaj poziv'))
                    ->emptyText(__('Nema dodanog poziva.'))
                    ->modalDescription(__('Unesite sliku, naslov i opis za istaknuti poziv.'))
                    ->showVisibility(false)
                    ->inlineForm(submitLabel: __('Spremi sadržaj'))
                    ->fields([
                        Field::image('image')
                            ->label(__('Slika'))
                            ->help(corexis_image_upload()->helpText().' '.__('Najbolje izgleda horizontalna fotografija radionice, sajma, školskog prostora ili zajedničkog rada.'))
                            ->size('max-w-sm aspect-[4/3]')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),

                        Field::text('title')
                            ->label(__('Naslov'))
                            ->required()
                            ->max(160),

                        Field::textarea('content')
                            ->label(__('Opis'))
                            ->rows(4)
                            ->nullable()
                            ->max(700),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati poziv?'))
                            ->confirmText(__('Poziv će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite kako će se isti poziv prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('banner')
                    ->variants([
                        LayoutVariant::add('banner')
                            ->label(__('Horizontalni banner'))
                            ->description(__('Široki CTA blok sa slikom lijevo i tamnom tekstualnom zonom desno.'))
                            ->option('preview', 'collaboration_banner'),

                        LayoutVariant::add('image_background')
                            ->label(__('Slika kao pozadina'))
                            ->description(__('Fotografija pokriva cijeli banner, a tekst stoji desno preko tamnog gradijenta.'))
                            ->option('preview', 'collaboration_background'),

                        LayoutVariant::add('image_card')
                            ->label(__('Kartica preko slike'))
                            ->description(__('Fotografija je pozadina bannera, a sadržaj je u svijetloj zaobljenoj kartici preko slike.'))
                            ->option('preview', 'collaboration_image_card'),
                    ]),

                Tab::settings(__('Postavke'))
                    ->heading(__('Postavke prikaza'))
                    ->description(__('Uredite prikaz i odredište gumba za ovaj poziv.'))
                    ->fields([
                        Field::boolean('show_button')
                            ->label(__('Prikaži gumb'))
                            ->help(__('Kada je uključeno, ispod opisa se prikazuje gumb s tekstom i poveznicom iz ovih postavki.'))
                            ->default(true),

                        Field::text('button_text')
                            ->label(__('Tekst gumba'))
                            ->help(__('Kratak poziv na akciju, npr. Javite nam se.'))
                            ->nullable()
                            ->max(80),

                        Field::text('button_url')
                            ->label(__('Poveznica gumba'))
                            ->nullable()
                            ->rules(['nullable', 'string', 'max:2048']),
                    ]),
            ]);
    }

    private function productListing(string $type, string $label, int $defaultLimit): AdminSection
    {
        $defaultSource = $type === 'featured_products' ? 'featured' : 'all';

        return $this->withDemo(AdminSection::add($type), $type)
            ->label($label)
            ->messages([
                'required' => __('Obavezno polje'),
                'layout_saved' => __('Izgled radova je spremljen.'),
                'settings_saved' => __('Postavke radova su spremljene.'),
            ])
            ->tabs([
                Tab::livewire(__('Sadržaj'), 'products.source-manager')
                    ->heading($label)
                    ->description(__('Ova sekcija prikazuje radove iz kataloga. Sadržaj, slike, vidljivost i isticanje uređuju se u modulu Radovi.')),

                Tab::settings(__('Filter'), 'filter')
                    ->heading(__('Odabir radova'))
                    ->description(__('Odaberite prikazuju li se svi radovi, samo istaknuti radovi ili radovi povezani s odabranim taxonomy vrijednostima.'))
                    ->option('icon', 'funnel')
                    ->fields([
                        Field::select('content_source')
                            ->label(__('Prikaži'))
                            ->help(__('Izvor možete promijeniti bez dodavanja nove sekcije.'))
                            ->default($defaultSource)
                            ->options([
                                ['value' => 'all', 'label' => __('Sve radove')],
                                ['value' => 'featured', 'label' => __('Istaknute radove')],
                                ['value' => 'taxonomy', 'label' => __('Radove prema taxonomy')],
                            ])
                            ->rules(['required', 'string', 'in:all,featured,taxonomy'])
                            ->storage('settings.content_source'),

                        Field::checkboxList('taxonomy_item_uuids')
                            ->label(__('Kategorije i oznake'))
                            ->help(__('Odaberite jednu ili više vrijednosti. Ako ništa nije odabrano, prikazuju se svi radovi.'))
                            ->default([])
                            ->optionsProvider(ProductTaxonomyFieldOptions::class)
                            ->visibleWhen('content_source', 'taxonomy')
                            ->storage('settings.taxonomy_item_uuids'),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite kako će se radovi prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Siguran katalog s jednakim slikama, nazivom, opisom i cijenom.'))
                            ->option('preview', 'products_cards'),

                        LayoutVariant::add('highlighted')
                            ->label(__('Istaknuti raspored'))
                            ->description(__('Jedan veći rad i dva uža rada u mirnom izložbenom rasporedu.'))
                            ->option('preview', 'products_highlighted'),

                        LayoutVariant::add('showcase')
                            ->label(__('Izložbeni izlog'))
                            ->description(__('Veći vizualni dojam s radovima složenima kao školska izložba.'))
                            ->option('preview', 'products_showcase'),

                        LayoutVariant::add('store_grid')
                            ->label(__('Prodajna mreža'))
                            ->description(__('Uredan izlog radova u tri stupca sa slikom, nazivom, opisom i cijenom.'))
                            ->option('preview', 'products_store_grid'),

                        LayoutVariant::add('scroll_showcase')
                            ->label(__('Vodoravni izlog'))
                            ->description(__('Mobilni vodoravni izlog koji na većim ekranima prelazi u urednu mrežu radova.'))
                            ->option('preview', 'products_scroll_showcase'),

                        LayoutVariant::add('catalog')
                            ->label(__('Katalog redovi'))
                            ->description(__('Pregledniji popis za veći broj radova, bez gubitka teksta.'))
                            ->option('preview', 'products_catalog'),

                        LayoutVariant::add('carousel')
                            ->label(__('Klizni prikaz radova'))
                            ->description(__('Animirani vodoravni niz radova s kontrolama. Dobar je kada želite malo više interakcije bez dodatnih postavki.'))
                            ->option('preview', 'products_carousel')
                            ->option('badge', __('Animacija'))
                            ->option('animated', true),
                    ]),

                Tab::settings(__('Postavke'))
                    ->heading(__('Postavke prikaza'))
                    ->description(__('Uredite koliko se radova prikazuje u ovoj sekciji.'))
                    ->fields([
                        Field::boolean('show_price')
                            ->label(__('Prikaži cijenu'))
                            ->help(__('Kada je uključeno, cijena rada prikazuje se u svim izgledima ove sekcije.'))
                            ->default(true)
                            ->storage('settings.show_price'),

                        Field::select('limit')
                            ->label(__('Broj radova'))
                            ->help(__('Odaberite koliko radova se prikazuje u ovoj sekciji.'))
                            ->default($defaultLimit)
                            ->options([
                                ['value' => 3, 'label' => __('3 rada')],
                                ['value' => 4, 'label' => __('4 rada')],
                                ['value' => 5, 'label' => __('5 radova')],
                                ['value' => 6, 'label' => __('6 radova')],
                                ['value' => 8, 'label' => __('8 radova')],
                                ['value' => 12, 'label' => __('12 radova')],
                            ])
                            ->rules(['required', 'integer', 'in:3,4,5,6,8,12'])
                            ->storage('settings.limit'),

                        Field::text('single_back_label')
                            ->label(__('Tekst povratne poveznice'))
                            ->help(__('Tekst poveznice na detalju rada koja vraća korisnika na popis radova.'))
                            ->default(__('Natrag na radove'))
                            ->max(80)
                            ->storage('settings.single_back_label'),

                        Field::text('single_previous_label')
                            ->label(__('Tekst za prethodni rad'))
                            ->help(__('Tekst koji se prikazuje iznad naslova prethodnog rada na detalju rada.'))
                            ->default(__('Prethodni rad'))
                            ->nullable()
                            ->max(80)
                            ->storage('settings.single_previous_label'),

                        Field::text('single_next_label')
                            ->label(__('Tekst za sljedeći rad'))
                            ->help(__('Tekst koji se prikazuje iznad naslova sljedećeg rada na detalju rada.'))
                            ->default(__('Sljedeći rad'))
                            ->nullable()
                            ->max(80)
                            ->storage('settings.single_next_label'),
                    ]),

                Tab::settings(__('Pojedinačni rad'), 'single')
                    ->heading(__('Izgled pojedinačnog rada'))
                    ->description(__('Uredite kako izgleda stranica kada posjetitelj otvori pojedini rad.'))
                    ->option('icon', 'document-magnifying-glass')
                    ->fields([
                        Field::select('single_layout')
                            ->label(__('Raspored'))
                            ->default('classic')
                            ->option('display', 'preview_cards')
                            ->options([
                                [
                                    'value' => 'classic',
                                    'label' => __('Klasični prikaz'),
                                    'description' => __('Naslov, cijena, velika slika i opis u mirnom redoslijedu.'),
                                    'preview' => 'product_single_classic',
                                ],
                                [
                                    'value' => 'split',
                                    'label' => __('Slika i opis u stupcima'),
                                    'description' => __('Slika i opis stoje jedan uz drugi na širim ekranima.'),
                                    'preview' => 'product_single_split',
                                ],
                                [
                                    'value' => 'editorial',
                                    'label' => __('Editorial prikaz'),
                                    'description' => __('Centriraniji prikaz s naglaskom na naslov i fotografiju rada.'),
                                    'preview' => 'product_single_editorial',
                                ],
                            ])
                            ->rules(['required', 'string', 'in:classic,split,editorial'])
                            ->storage('settings.single_layout'),
                    ]),
            ]);
    }

    private function galleryListing(string $type): AdminSection
    {
        $label = $type === 'gallery_grid' ? __('Galerija') : __('Istaknuti albumi');
        $heading = $type === 'gallery_grid' ? __('Galerija') : __('Istaknuti albumi');
        $description = $type === 'gallery_grid'
            ? __('Prikažite veći pregled postojećih albuma iz modula Galerija. Ovo je najbolji izbor za glavnu stranicu galerije.')
            : __('Prikažite nekoliko postojećih albuma iz modula Galerija. Ovo je dobar izbor za naslovnicu ili kratki vizualni blok.');

        return $this->withDemo(AdminSection::add($type), $type)
            ->label($label)
            ->messages([
                'required' => __('Obavezno polje'),
                'layout_saved' => __('Izgled galerije je spremljen.'),
                'settings_saved' => __('Postavke galerije su spremljene.'),
            ])
            ->tabs([
                Tab::settings(__('Izvor'), 'source')
                    ->heading(__('Izvor fotografija'))
                    ->description(__('Prikažite postojeće albume ili učitajte fotografije koje pripadaju samo ovoj sekciji.'))
                    ->option('icon', 'funnel')
                    ->fields([
                        Field::select('content_source')
                            ->label(__('Prikaži'))
                            ->default('albums')
                            ->options([
                                ['value' => 'albums', 'label' => __('Postojeće galerije')],
                                ['value' => 'direct', 'label' => __('Fotografije učitane u sekciju')],
                            ])
                            ->rules(['required', 'string', 'in:albums,direct'])
                            ->storage('settings.content_source'),

                        Field::checkboxList('gallery_uuids')
                            ->label(__('Odabrane galerije'))
                            ->help(__('Ako ništa ne odaberete, prikazat će se sve dostupne galerije.'))
                            ->default([])
                            ->optionsProvider(GalleryFieldOptions::class)
                            ->visibleWhen('content_source', 'albums')
                            ->storage('settings.gallery_uuids'),
                    ]),

                Tab::livewire(__('Sadržaj'), 'gallery.source-manager')
                    ->heading($heading)
                    ->description($description.' '.__('Fotografije i albumi uređuju se na popisu galerija.'))
                    ->visibleWhen('content_source', 'albums'),

                Tab::view(__('Fotografije'), 'admin.pages.sections.photo-gallery-tab', 'photos')
                    ->heading(__('Fotografije u sekciji'))
                    ->description(__('Dodajte fotografije izravno u ovu sekciju bez stvaranja zasebnog albuma.'))
                    ->option('icon', 'photo')
                    ->visibleWhen('content_source', 'direct'),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite kako će se galerije prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Siguran prikaz galerija u jednakim karticama s naslovom i brojem fotografija.'))
                            ->option('preview', 'gallery_cards')
                            ->visibleWhen('content_source', 'albums'),

                        LayoutVariant::add('text_cards')
                            ->label(__('Opisne kartice'))
                            ->description(__('Kartice s tekstom iznad fotografije, dobre za miran pregled albuma.'))
                            ->option('preview', 'gallery_text_cards')
                            ->visibleWhen('content_source', 'albums'),

                        LayoutVariant::add('masonry')
                            ->label(__('Foto stupci'))
                            ->description(__('Masonry prikaz naslovnih fotografija albuma u uskim stupcima.'))
                            ->option('preview', 'gallery_masonry')
                            ->visibleWhen('content_source', 'albums'),

                        LayoutVariant::add('featured')
                            ->label(__('Istaknuti mozaik'))
                            ->description(__('Jedna velika galerija uz dvije manje, s naslovom i brojem fotografija ispod slike.'))
                            ->option('preview', 'gallery_featured')
                            ->visibleWhen('content_source', 'albums'),

                        LayoutVariant::add('wall')
                            ->label(__('Zid trenutaka'))
                            ->description(__('Vizualniji prikaz koji djeluje kao uredno složena školska izložba.'))
                            ->option('preview', 'gallery_wall')
                            ->visibleWhen('content_source', 'albums'),

                        LayoutVariant::add('journal')
                            ->label(__('Dnevnik rada'))
                            ->description(__('Mirniji popis galerija s većim prostorom za opis.'))
                            ->option('preview', 'gallery_journal')
                            ->visibleWhen('content_source', 'albums'),

                        LayoutVariant::add('carousel')
                            ->label(__('Klizni prikaz galerija'))
                            ->description(__('Animirani vodoravni niz galerija s kontrolama za pregled više albuma.'))
                            ->option('preview', 'gallery_carousel')
                            ->option('badge', __('Animacija'))
                            ->option('animated', true)
                            ->visibleWhen('content_source', 'albums'),

                        LayoutVariant::add('grid_2x2')
                            ->label(__('2 stupca'))
                            ->description(__('Fotografije u jednostavnoj dvostupčanoj mreži.'))
                            ->option('preview', 'photo_gallery_grid_2x2')
                            ->visibleWhen('content_source', 'direct'),

                        LayoutVariant::add('grid_3x3')
                            ->label(__('3 stupca'))
                            ->description(__('Fotografije u kompaktnoj mreži s tri stupca.'))
                            ->option('preview', 'photo_gallery_grid_3x3')
                            ->visibleWhen('content_source', 'direct'),

                        LayoutVariant::add('grid')
                            ->label(__('4 stupca'))
                            ->description(__('Bogata mreža fotografija u četiri stupca.'))
                            ->option('preview', 'photo_gallery_grid')
                            ->visibleWhen('content_source', 'direct'),

                        LayoutVariant::add('photo_featured')
                            ->label(__('Istaknuta prva'))
                            ->description(__('Prva fotografija je veća, a ostale je prate.'))
                            ->option('preview', 'photo_gallery_featured')
                            ->visibleWhen('content_source', 'direct'),

                        LayoutVariant::add('mosaic')
                            ->label(__('Mozaik'))
                            ->description(__('Dinamičniji raspored s različitim veličinama fotografija.'))
                            ->option('preview', 'photo_gallery_mosaic')
                            ->visibleWhen('content_source', 'direct'),

                        LayoutVariant::add('photo_carousel')
                            ->label(__('Klizni prikaz fotografija'))
                            ->description(__('Vodoravni niz fotografija s kontrolama.'))
                            ->option('preview', 'photo_gallery_carousel')
                            ->option('badge', __('Animacija'))
                            ->option('animated', true)
                            ->visibleWhen('content_source', 'direct'),
                    ]),

                Tab::settings(__('Postavke'))
                    ->heading(__('Postavke prikaza'))
                    ->description(__('Uredite koliko se galerija prikazuje u ovoj sekciji.'))
                    ->fields([
                        Field::select('limit')
                            ->label(__('Broj galerija'))
                            ->help(__('Odaberite koliko galerija se prikazuje u ovoj sekciji.'))
                            ->default(12)
                            ->options([
                                ['value' => 3, 'label' => __('3 galerije')],
                                ['value' => 4, 'label' => __('4 galerije')],
                                ['value' => 6, 'label' => __('6 galerija')],
                                ['value' => 8, 'label' => __('8 galerija')],
                                ['value' => 12, 'label' => __('12 galerija')],
                            ])
                            ->rules(['required', 'integer', 'in:3,4,6,8,12'])
                            ->visibleWhen('content_source', 'albums')
                            ->storage('settings.limit'),

                        Field::select('image_ratio')
                            ->label(__('Omjer fotografija'))
                            ->default('four_three')
                            ->options([
                                ['value' => 'four_three', 'label' => __('4:3')],
                                ['value' => 'three_two', 'label' => __('3:2')],
                                ['value' => 'square', 'label' => __('Kvadrat')],
                                ['value' => 'video', 'label' => __('16:9')],
                            ])
                            ->rules(['required', 'string', 'in:four_three,three_two,square,video'])
                            ->visibleWhen('content_source', 'direct')
                            ->storage('settings.image_ratio'),

                        Field::boolean('show_captions')
                            ->label(__('Prikaži opise fotografija'))
                            ->default(false)
                            ->visibleWhen('content_source', 'direct')
                            ->storage('settings.show_captions'),
                    ]),

                Tab::settings(__('Otvorena galerija'), 'single')
                    ->heading(__('Izgled otvorene galerije'))
                    ->description(__('Uredite kako izgleda stranica kada posjetitelj otvori pojedini album.'))
                    ->option('icon', 'photo')
                    ->visibleWhen('content_source', 'albums')
                    ->fields([
                        Field::select('single_photo_layout')
                            ->label(__('Prikaz fotografija u otvorenoj galeriji'))
                            ->help(__('Odaberite kako se sličice prikazuju kada posjetitelj otvori pojedinu galeriju.'))
                            ->default('masonry')
                            ->option('display', 'preview_cards')
                            ->options([
                                [
                                    'value' => 'masonry',
                                    'label' => __('Foto mozaik'),
                                    'description' => __('Nepravilni stupci za dinamičan pregled fotografija.'),
                                    'preview' => 'gallery_single_masonry',
                                ],
                                [
                                    'value' => 'featured',
                                    'label' => __('Istaknuti mozaik'),
                                    'description' => __('Jedna veća fotografija uz manje sličice sa strane.'),
                                    'preview' => 'gallery_single_featured',
                                ],
                                [
                                    'value' => 'grid',
                                    'label' => __('Uredna mreža'),
                                    'description' => __('Mirna mreža jednakih sličica za pregledniji album.'),
                                    'preview' => 'gallery_single_grid',
                                ],
                            ])
                            ->rules(['required', 'string', 'in:masonry,featured,grid'])
                            ->storage('settings.single_photo_layout'),
                    ]),
            ]);
    }

    private function photoGallery(): AdminSection
    {
        return $this->withDemo(AdminSection::add('photo_gallery'), 'photo_gallery')
            ->label(__('Fotografije u sekciji'))
            ->messages([
                'required' => __('Obavezno polje'),
                'layout_saved' => __('Izgled foto galerije je spremljen.'),
                'settings_saved' => __('Postavke foto galerije su spremljene.'),
            ])
            ->tabs([
                Tab::view(__('Fotografije'), 'admin.pages.sections.photo-gallery-tab', 'photos')
                    ->heading(__('Fotografije u sekciji'))
                    ->description(__('Dodajte fotografije izravno u ovu sekciju, npr. za jednu radionicu, projekt ili događaj. Ne stvarate novi album u Galeriji.'))
                    ->option('icon', 'photo'),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled foto galerije'))
                    ->description(__('Odaberite kako će se fotografije prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('grid_2x2')
                    ->variants([
                        LayoutVariant::add('grid_2x2')
                            ->label(__('2 stupca'))
                            ->description(__('Sve fotografije u jednostavnoj dvostupčanoj mreži. Dobro za mirniji pregled.'))
                            ->option('preview', 'photo_gallery_grid_2x2'),

                        LayoutVariant::add('grid_3x3')
                            ->label(__('3 stupca'))
                            ->description(__('Sve fotografije u mirnoj mreži s tri stupca. Dobro kada želite kompaktniji pregled.'))
                            ->option('preview', 'photo_gallery_grid_3x3'),

                        LayoutVariant::add('grid')
                            ->label(__('4 stupca'))
                            ->description(__('Sve fotografije u jasnoj mreži s četiri stupca. Dobro za bogatiji pregled sekcije.'))
                            ->option('preview', 'photo_gallery_grid'),

                        LayoutVariant::add('featured')
                            ->label(__('Istaknuta prva'))
                            ->description(__('Prva fotografija je veća, a ostale je prate kao manji zapisi.'))
                            ->option('preview', 'photo_gallery_featured'),

                        LayoutVariant::add('mosaic')
                            ->label(__('Mozaik'))
                            ->description(__('Dinamičniji raspored s različitim veličinama fotografija.'))
                            ->option('preview', 'photo_gallery_mosaic'),

                        LayoutVariant::add('carousel')
                            ->label(__('Klizni prikaz fotografija'))
                            ->description(__('Animirani vodoravni niz fotografija s kontrolama. Zadržava lightbox prikaz kao ostali izgledi.'))
                            ->option('preview', 'photo_gallery_carousel')
                            ->option('badge', __('Animacija'))
                            ->option('animated', true),
                    ]),

                Tab::settings(__('Postavke'))
                    ->heading(__('Postavke prikaza'))
                    ->description(__('Uredite omjer prikaza i prikaz opisa ispod fotografija.'))
                    ->fields([
                        Field::select('image_ratio')
                            ->label(__('Omjer fotografija'))
                            ->help(__('Odaberite kako se fotografije režu u mreži.'))
                            ->default('four_three')
                            ->options([
                                ['value' => 'four_three', 'label' => __('4:3')],
                                ['value' => 'three_two', 'label' => __('3:2')],
                                ['value' => 'square', 'label' => __('Kvadrat')],
                                ['value' => 'video', 'label' => __('16:9')],
                            ])
                            ->rules(['required', 'string', 'in:four_three,three_two,square,video'])
                            ->storage('settings.image_ratio'),

                        Field::boolean('show_captions')
                            ->label(__('Prikaži opise fotografija'))
                            ->help(__('Ako fotografija ima upisan naslov ili opis, prikazat će se ispod slike.'))
                            ->default(false)
                            ->storage('settings.show_captions'),
                    ]),
            ]);
    }

    private function video(): AdminSection
    {
        return $this->withDemo(AdminSection::add('video'), 'video')
            ->label(__('Video'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Video je spremljen.'),
                'layout_saved' => __('Izgled video sekcije je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Video'))
                    ->description(__('Dodajte YouTube video zapise koji će se prikazati kao sličice i otvoriti u modalu na javnoj stranici.'))
                    ->formTitle(__('Uredi video'), __('Dodaj video'))
                    ->addLabel(__('Dodaj video'))
                    ->emptyText(__('Nema dodanih video zapisa.'))
                    ->modalDescription(__('Zalijepite YouTube link. Sustav će automatski pripremiti video ID, embed poveznicu i sličicu.'))
                    ->singleColumn()
                    ->hideSortOrder()
                    ->fields([
                        Field::text('title')
                            ->label(__('Naziv'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Opis'))
                            ->rows(4)
                            ->nullable()
                            ->max(1000),

                        Field::url('youtube_url')
                            ->label(__('YouTube URL'))
                            ->required()
                            ->max(2048),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati video?'))
                            ->confirmText(__('Odabrani video će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled video sekcije'))
                    ->description(__('Odaberite kako će se video zapisi posložiti na javnoj stranici. Svi izgledi koriste iste video zapise i otvaraju ih u velikom modalu.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->submitLabel(__('Spremi izgled'))
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Kartice'))
                            ->description(__('Uredan grid s video sličicom, nazivom i kratkim opisom. Dobar je kada imate dva ili više ravnopravnih video zapisa.'))
                            ->option('preview', 'video_cards'),

                        LayoutVariant::add('featured')
                            ->label(__('Istaknuti video'))
                            ->description(__('Prvi video dobiva veći prostor, a ostali se prikazuju kao kraći popis sa strane. Korisno za najvažniji video.'))
                            ->option('preview', 'video_featured'),

                        LayoutVariant::add('list')
                            ->label(__('Popis'))
                            ->description(__('Kompaktni redovi sa sličicom lijevo i tekstom desno. Dobar je za stranice s više edukativnih ili informativnih videa.'))
                            ->option('preview', 'video_list'),

                        LayoutVariant::add('focus')
                            ->label(__('Fokus'))
                            ->description(__('Jedan veliki video je u prvom planu, a dodatni video zapisi ostaju ispod njega kao manje kartice.'))
                            ->option('preview', 'video_focus'),

                        LayoutVariant::add('grid_3x2')
                            ->label(__('3 gore / 3 dolje'))
                            ->description(__('Grid s tri video kartice u redu. Najbolje izgleda kada imate šest ili više kratkih video zapisa.'))
                            ->option('preview', 'video_grid_3x2'),

                        LayoutVariant::add('grid_4x2')
                            ->label(__('4 gore / 4 dolje'))
                            ->description(__('Gušći grid s četiri video kartice u redu. Dobar je za arhivu ili stranicu s većim brojem video zapisa.'))
                            ->option('preview', 'video_grid_4x2'),
                    ]),
            ]);
    }

    private function calendar(): AdminSection
    {
        return $this->withDemo(AdminSection::add('calendar'), 'calendar')
            ->label(__('Kalendar'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Događaj je spremljen.'),
                'layout_saved' => __('Izgled kalendara je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Kalendar'))
                    ->description(__('Dodajte radionice, izložbe, sajmove, školske događaje i druge najavljene aktivnosti zadruge.'))
                    ->formTitle(__('Uredi događaj'), __('Dodaj događaj'))
                    ->addLabel(__('Dodaj događaj'))
                    ->emptyText(__('Nema dodanih događaja.'))
                    ->modalDescription(__('Unesite datum, vrijeme, naziv, opis i lokaciju događaja.'))
                    ->modalFlyout()
                    ->singleColumn()
                    ->fields([
                        Field::date('event_date')
                            ->label(__('Datum događaja'))
                            ->required(),

                        Field::time('starts_at')
                            ->label(__('Vrijeme početka'))
                            ->nullable(),

                        Field::time('ends_at')
                            ->label(__('Vrijeme završetka'))
                            ->nullable(),

                        Field::text('title')
                            ->label(__('Naslov događaja'))
                            ->required()
                            ->max(255),

                        Field::textarea('description')
                            ->label(__('Opis događaja'))
                            ->rows(4)
                            ->nullable()
                            ->max(1000),

                        Field::text('location')
                            ->label(__('Lokacija'))
                            ->nullable()
                            ->max(255),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati događaj?'))
                            ->confirmText(__('Odabrani događaj će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled kalendara'))
                    ->description(__('Odaberite kako će se događaji prikazati na javnoj stranici. Svi izgledi koriste iste događaje.'))
                    ->storage('settings.layout_variant')
                    ->default('calendar-split')
                    ->submitLabel(__('Spremi izgled'))
                    ->variants([
                        LayoutVariant::add('calendar-split')
                            ->label(__('Split prikaz'))
                            ->description(__('Uvodni tekst lijevo i pregledan popis najbližih događaja desno. Dobar je za naslovnicu.'))
                            ->option('preview', 'calendar_split'),

                        LayoutVariant::add('calendar-list')
                            ->label(__('Timeline prikaz'))
                            ->description(__('Događaji su prikazani kao mirna vremenska crta s datumima lijevo i sadržajem desno.'))
                            ->option('preview', 'calendar_list'),

                        LayoutVariant::add('calendar-cards')
                            ->label(__('Kartice događaja'))
                            ->description(__('Jednake kartice u gridu za veći broj radionica, izložbi i aktivnosti.'))
                            ->option('preview', 'calendar_cards'),

                        LayoutVariant::add('calendar-carousel')
                            ->label(__('Klizni prikaz događaja'))
                            ->description(__('Animirani vodoravni niz događaja s kontrolama, bez dodatnih postavki.'))
                            ->option('preview', 'calendar_carousel')
                            ->option('badge', __('Animacija'))
                            ->option('animated', true),
                    ]),
            ]);
    }

    private function newsListing(string $type, string $label, int $defaultLimit): AdminSection
    {
        $filterTabKey = $type === 'taxonomy_news' ? 'taxonomy' : 'filter';
        $defaultSource = match ($type) {
            'featured_news' => 'featured',
            'taxonomy_news' => 'taxonomy',
            default => 'all',
        };
        $sourceDescription = match ($type) {
            'featured_news' => __('Prikažite ručno istaknute objave iz modula Objave. Ovo je dobar izbor za naslovnicu, važnu najavu ili posebnu priču.'),
            'taxonomy_news' => __('Prikažite objave povezane s odabranim kategorijama ili oznakama. Ovo je dobar izbor za tematske stranice i izdvojene cjeline.'),
            default => __('Prikažite objave iz modula Objave. Ovo je najbolji izbor za glavnu stranicu Objave.'),
        };

        return $this->withDemo(AdminSection::add($type), $type)
            ->label($label)
            ->messages([
                'required' => __('Obavezno polje'),
                'layout_saved' => __('Izgled objava je spremljen.'),
                'settings_saved' => __('Postavke objava su spremljene.'),
            ])
            ->tabs([
                Tab::livewire(__('Sadržaj'), 'blog.source-manager')
                    ->heading($label)
                    ->description($sourceDescription.' '.__('Sadržaj se uređuje na popisu objava.'))
                    ->parameters([]),

                Tab::settings(__('Filter'), $filterTabKey)
                    ->heading(__('Odabir objava'))
                    ->description(__('Odaberite prikazuju li se sve objave, samo istaknute objave ili objave prema kategorijama i oznakama.'))
                    ->option('icon', 'funnel')
                    ->fields([
                        Field::select('content_source')
                            ->label(__('Prikaži'))
                            ->help(__('Izvor možete promijeniti bez dodavanja nove sekcije.'))
                            ->default($defaultSource)
                            ->options([
                                ['value' => 'all', 'label' => __('Sve objave')],
                                ['value' => 'featured', 'label' => __('Istaknute objave')],
                                ['value' => 'taxonomy', 'label' => __('Objave prema kategoriji ili oznaci')],
                            ])
                            ->rules(['required', 'string', 'in:all,featured,taxonomy'])
                            ->storage('settings.content_source'),

                        Field::checkboxList('taxonomy_item_uuids')
                            ->label(__('Kategorije i oznake'))
                            ->help(__('Možete odabrati više vrijednosti iz različitih grupa. Ako ništa nije odabrano, prikazuju se sve objave.'))
                            ->default([])
                            ->optionsProvider(BlogTaxonomyFieldOptions::class)
                            ->visibleWhen('content_source', 'taxonomy')
                            ->storage('settings.taxonomy_item_uuids'),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite kako će se objave prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Kartice'))
                            ->description(__('Jednostavan mrežni prikaz s fotografijom iznad teksta.'))
                            ->option('preview', 'news_cards'),

                        LayoutVariant::add('featured')
                            ->label(__('Istaknuto'))
                            ->description(__('Prva objava je velika, a ostale su manje uz nju.'))
                            ->option('preview', 'news_featured'),

                        LayoutVariant::add('stacked')
                            ->label(__('Jedna iznad dvije'))
                            ->description(__('Jedna velika objava gore, a dvije objave ispod nje u dva stupca.'))
                            ->option('preview', 'news_stacked'),

                        LayoutVariant::add('journal')
                            ->label(__('Dnevnik'))
                            ->description(__('Mirniji popis objava s većim fokusom na naslov i tekst.'))
                            ->option('preview', 'news_journal'),

                        LayoutVariant::add('blog_grid')
                            ->label(__('Blog mreža'))
                            ->description(__('Prozračan blog prikaz s velikom slikom, datumom, naslovom, opisom i autorom.'))
                            ->option('preview', 'news_blog_grid'),

                        LayoutVariant::add('image_cards')
                            ->label(__('Foto kartice'))
                            ->description(__('Svjetlije foto kartice s naslovom i pomoćnim podacima preko slike.'))
                            ->option('preview', 'news_image_cards'),

                        LayoutVariant::add('editorial_list')
                            ->label(__('Editorial redovi'))
                            ->description(__('Prostrani redovi s većom slikom lijevo i tekstom objave desno.'))
                            ->option('preview', 'news_editorial_list'),
                    ]),

                Tab::settings(__('Postavke'))
                    ->heading(__('Postavke prikaza'))
                    ->description(__('Uredite koliko se objava prikazuje i koji se pomoćni podaci prikazuju u ovoj sekciji.'))
                    ->fields([
                        Field::select('limit')
                            ->label(__('Broj objava'))
                            ->help(__('Odaberite koliko objava se prikazuje u ovoj sekciji.'))
                            ->default($defaultLimit)
                            ->options([
                                ['value' => 3, 'label' => __('3 objave')],
                                ['value' => 6, 'label' => __('6 objava')],
                                ['value' => 12, 'label' => __('12 objava')],
                            ])
                            ->rules(['required', 'integer', 'in:3,6,12'])
                            ->storage('settings.limit'),

                        Field::text('read_more_label')
                            ->label(__('Tekst poveznice'))
                            ->help(__('Tekst koji se prikazuje umjesto zadane poveznice Pročitaj objavu.'))
                            ->default(__('Pročitaj objavu'))
                            ->nullable()
                            ->max(80)
                            ->storage('settings.read_more_label'),

                        Field::text('load_more_label')
                            ->label(__('Tekst gumba'))
                            ->help(__('Tekst gumba za učitavanje dodatnih objava.'))
                            ->default(__('Prikaži više'))
                            ->nullable()
                            ->max(80)
                            ->storage('settings.load_more_label'),

                        Field::boolean('show_author')
                            ->label(__('Prikaži autora objave'))
                            ->help(__('Ako je autor upisan u podacima objave, prikazat će se uz karticu.'))
                            ->default(false)
                            ->storage('settings.show_author'),

                        Field::boolean('show_date')
                            ->label(__('Prikaži datum objave'))
                            ->help(__('Datum se prikazuje u hrvatskom obliku.'))
                            ->default(false)
                            ->storage('settings.show_date'),

                        Field::text('single_back_label')
                            ->label(__('Tekst povratne poveznice'))
                            ->help(__('Tekst poveznice na detalju objave koja vraća korisnika na popis objava.'))
                            ->default(__('Natrag na objave'))
                            ->nullable()
                            ->max(80)
                            ->storage('settings.single_back_label'),

                        Field::text('single_previous_label')
                            ->label(__('Tekst za prethodnu objavu'))
                            ->help(__('Tekst koji se prikazuje iznad naslova prethodne objave na detalju objave.'))
                            ->default(__('Prethodna objava'))
                            ->nullable()
                            ->max(80)
                            ->storage('settings.single_previous_label'),

                        Field::text('single_next_label')
                            ->label(__('Tekst za sljedeću objavu'))
                            ->help(__('Tekst koji se prikazuje iznad naslova sljedeće objave na detalju objave.'))
                            ->default(__('Sljedeća objava'))
                            ->nullable()
                            ->max(80)
                            ->storage('settings.single_next_label'),
                    ]),

                Tab::settings(__('Pojedinačna objava'), 'single')
                    ->heading(__('Izgled pojedinačne objave'))
                    ->description(__('Uredite kako izgleda stranica kada posjetitelj otvori pojedinu objavu.'))
                    ->option('icon', 'document-text')
                    ->fields([
                        Field::select('single_layout')
                            ->label(__('Raspored'))
                            ->default('classic')
                            ->option('display', 'preview_cards')
                            ->options([
                                [
                                    'value' => 'classic',
                                    'label' => __('Klasični prikaz'),
                                    'description' => __('Naslov, meta podaci, fotografija i tekst u standardnom toku.'),
                                    'preview' => 'post_single_classic',
                                ],
                                [
                                    'value' => 'hero',
                                    'label' => __('Hero prikaz'),
                                    'description' => __('Veći naslov i snažniji uvod za važnije objave.'),
                                    'preview' => 'post_single_hero',
                                ],
                                [
                                    'value' => 'compact',
                                    'label' => __('Sažeti editorial prikaz'),
                                    'description' => __('Uži, mirniji prikaz za kraće objave i obavijesti.'),
                                    'preview' => 'post_single_compact',
                                ],
                                [
                                    'value' => 'cover',
                                    'label' => __('Naslovna fotografija'),
                                    'description' => __('Velika fotografija s naslovom preko slike i tekstom ispod nje.'),
                                    'preview' => 'post_single_cover',
                                ],
                                [
                                    'value' => 'sidebar',
                                    'label' => __('Tekst s bočnim podacima'),
                                    'description' => __('Čitateljski prikaz s tekstom u glavnom stupcu i podacima sa strane.'),
                                    'preview' => 'post_single_sidebar',
                                ],
                            ])
                            ->rules(['required', 'string', 'in:classic,hero,compact,cover,sidebar'])
                            ->storage('settings.single_layout'),
                    ]),
            ]);
    }

    private function testimonials(): AdminSection
    {
        return $this->withDemo(AdminSection::add('testimonials'), 'testimonials')
            ->label(__('Dojmovi iz zadruge'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled dojmova je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Dojmovi iz zadruge'))
                    ->description(__('Uredite kratke izjave djece, roditelja ili mentora koje se prikazuju u ovoj sekciji.'))
                    ->formTitle(__('Uredi izjavu'), __('Dodaj izjavu'))
                    ->addLabel(__('Dodaj izjavu'))
                    ->emptyText(__('Nema dodanih izjava.'))
                    ->modalDescription(__('Unesite podatke za ovu izjavu.'))
                    ->modalFlyout()
                    ->singleColumn()
                    ->hideSortOrder()
                    ->fields([
                        Field::text('title')
                            ->label(__('Ime ili potpis'))
                            ->required()
                            ->max(255),

                        Field::text('subtitle')
                            ->label(__('Opis osobe'))
                            ->nullable()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Izjava'))
                            ->rows(4)
                            ->required(),

                        Field::image('image')
                            ->label(__('Slika ili logo'))
                            ->help(corexis_image_upload()->helpText().' '.__('Za dojam je najbolja manja fotografija ili logo na svijetloj pozadini.'))
                            ->size('small')
                            ->fit('contain')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati izjavu?'))
                            ->confirmText(__('Odabrana izjava će se premjestiti u arhivu. Možete je kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled dojmova iz zadruge'))
                    ->description(__('Odaberite kako će se dojmovi prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Siguran i uredan prikaz s jednakim karticama i cijelim tekstom dojma.')),

                        LayoutVariant::add('portraits')
                            ->label(__('Portreti u nizu'))
                            ->description(__('Okrugle fotografije iznad kratkih izjava u mirnom vodoravnom nizu.')),

                        LayoutVariant::add('quotes')
                            ->label(__('Citati u nizu'))
                            ->description(__('Naglašeni navodnici, izjava i potpis osobe u čistim stupcima.')),

                        LayoutVariant::add('split_grid')
                            ->label(__('Podijeljena mreža'))
                            ->description(__('Prozračan 2x2 raspored s fotografijom i kratkom izjavom.')),

                        LayoutVariant::add('spotlight')
                            ->label(__('Istaknuti dojam'))
                            ->description(__('Jedan dojam dobiva veći prostor, a ostali ga prate kao kraći popis.')),

                        LayoutVariant::add('featured_mosaic')
                            ->label(__('Istaknuti mozaik'))
                            ->description(__('Jedan veći dojam uz kraće izjave raspoređene u mirnom mozaiku.'))
                            ->option('preview', 'testimonials_featured_mosaic'),

                        LayoutVariant::add('notes')
                            ->label(__('Radioničke bilješke'))
                            ->description(__('Kreativniji prikaz koji podsjeća na uredno složene dječje bilješke.')),

                        LayoutVariant::add('masonry_columns')
                            ->label(__('Stupčani mozaik'))
                            ->description(__('Dojmovi se slažu u urednu mrežu od tri stupca na velikim ekranima.'))
                            ->option('preview', 'testimonials_masonry_columns'),

                        LayoutVariant::add('carousel')
                            ->label(__('Klizni prikaz dojmova'))
                            ->description(__('Animirani vodoravni niz dojmova s kontrolama. Dobar je za naslovnicu i kraće izjave.'))
                            ->option('preview', 'testimonials_carousel')
                            ->option('badge', __('Animacija'))
                            ->option('animated', true),
                    ]),
            ]);
    }

    private function faq(): AdminSection
    {
        return $this->withDemo(AdminSection::add('faq'), 'faq')
            ->label(__('Česta pitanja'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled čestih pitanja je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Pitanja i odgovori'))
                    ->description(__('Uredite česta pitanja koja se prikazuju u ovoj sekciji.'))
                    ->formTitle(__('Uredi pitanje'), __('Dodaj pitanje'))
                    ->addLabel(__('Dodaj pitanje'))
                    ->emptyText(__('Nema dodanih pitanja.'))
                    ->modalDescription(__('Unesite pitanje i odgovor koji će se prikazati u ovoj sekciji.'))
                    ->hideSortOrder()
                    ->showVisibility(false)
                    ->fields([
                        Field::text('title')
                            ->label(__('Pitanje'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Odgovor'))
                            ->rows(7)
                            ->required(),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati pitanje?'))
                            ->confirmText(__('Odabrano pitanje će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled čestih pitanja'))
                    ->description(__('Odaberite kako će se pitanja i odgovori prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Siguran prikaz pitanja u jednakim, čitkim karticama.'))
                            ->option('preview', 'faq_cards'),

                        LayoutVariant::add('accordion')
                            ->label(__('Mirni popis'))
                            ->description(__('Pregledan popis pitanja i odgovora za veći broj zapisa.')),

                        LayoutVariant::add('notebook')
                            ->label(__('Bilježnica'))
                            ->description(__('Topliji izgled prilagođen školskom i dječjem kontekstu.')),

                        LayoutVariant::add('compact_accordion')
                            ->label(__('Sažeti popis'))
                            ->description(__('Kompaktan accordion s ikonom pitanja i jasnim otvaranjem odgovora.')),

                        LayoutVariant::add('answer_grid')
                            ->label(__('Mreža odgovora'))
                            ->description(__('Pitanja i odgovori prikazani u čistoj dvostupčanoj mreži.')),

                        LayoutVariant::add('expanded_accordion')
                            ->label(__('Otvoreni popis'))
                            ->description(__('Širi accordion s prvim odgovorom otvorenim po učitavanju.')),

                        LayoutVariant::add('timeline_accordion')
                            ->label(__('Linija pitanja'))
                            ->description(__('Pitanja prikazana kao miran popis s otvaranjem odgovora po stavci.')),
                    ]),
            ]);
    }

    private function features(): AdminSection
    {
        return $this->withDemo(AdminSection::add('features'), 'features')
            ->label(__('Naše aktivnosti'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled aktivnosti je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Naše aktivnosti'))
                    ->description(__('Uredite glavne aktivnosti zadruge koje će se prikazati na javnoj stranici.'))
                    ->formTitle(__('Uredi aktivnost'), __('Dodaj aktivnost'))
                    ->addLabel(__('Dodaj aktivnost'))
                    ->emptyText(__('Nema dodanih aktivnosti.'))
                    ->modalDescription(__('Unesite naziv, kratki opis, fotografiju i ikonu za ovu aktivnost.'))
                    ->modalFlyout()
                    ->singleColumn()
                    ->hideSortOrder()
                    ->fields([
                        Field::text('title')
                            ->label(__('Naziv aktivnosti'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Kratki opis aktivnosti'))
                            ->rows(7)
                            ->nullable(),

                        Field::image('image')
                            ->label(__('Fotografija aktivnosti'))
                            ->help(corexis_image_upload()->helpText())
                            ->size('small')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),

                        Field::icon('icon')
                            ->label(__('Ikona aktivnosti'))
                            ->nullable()
                            ->max(255),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati aktivnost?'))
                            ->confirmText(__('Odabrana aktivnost će se premjestiti u arhivu. Možete je kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled aktivnosti'))
                    ->description(__('Odaberite izgled kojim će se iste aktivnosti prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('mosaic')
                    ->variants([
                        LayoutVariant::add('mosaic')
                            ->label(__('Jednake kartice'))
                            ->description(__('Uredan prikaz u jednakim karticama sa slikom, naslovom i cijelim opisom.'))
                            ->option('preview', 'features_mosaic'),

                        LayoutVariant::add('photo_cards')
                            ->label(__('Fotografske kartice'))
                            ->description(__('Kartice s fotografijom, istaknutom ikonom i kratkim opisom aktivnosti.'))
                            ->option('preview', 'features_photo_cards'),

                        LayoutVariant::add('editorial')
                            ->label(__('Urednički niz'))
                            ->description(__('Široke fotografije s numeriranim opisima u mirnom časopisnom ritmu.'))
                            ->option('preview', 'features_editorial'),

                        LayoutVariant::add('spotlight')
                            ->label(__('Istaknuti pregled'))
                            ->description(__('Prva aktivnost je velika, a ostale se nastavljaju u urednoj fotografskoj mreži.'))
                            ->option('preview', 'features_spotlight'),

                        LayoutVariant::add('alternating')
                            ->label(__('Naizmjenični redovi'))
                            ->description(__('Vodoravni redovi u kojima se fotografija i tekst izmjenjuju bez numeracije.'))
                            ->option('preview', 'features_alternating'),

                        LayoutVariant::add('path')
                            ->label(__('Put stvaranja'))
                            ->description(__('Naizmjenični prikaz koji djeluje kao mirna priča kroz aktivnosti.'))
                            ->option('preview', 'features_path'),

                        LayoutVariant::add('studio')
                            ->label(__('Studio ploča'))
                            ->description(__('Topliji prikaz s fotografijama i opisima kao pregled radioničkog dana.'))
                            ->option('preview', 'features_studio'),
                    ]),
            ]);
    }

    private function partners(): AdminSection
    {
        return $this->withDemo(AdminSection::add('partners'), 'partners')
            ->label(__('Partneri'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled partnera je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Partneri'))
                    ->description(__('Dodajte organizacije, ustanove, donatore i suradnike koji podržavaju rad udruge. Za svakog partnera možete upisati naziv, opis suradnje, logo ili poveznicu.'))
                    ->formTitle(__('Uredi partnera'), __('Dodaj partnera'))
                    ->addLabel(__('Dodaj partnera'))
                    ->emptyText(__('Nema dodanih partnera.'))
                    ->modalDescription(__('Unesite podatke za ovog partnera.'))
                    ->showVisibility(false)
                    ->fields([
                        Field::text('title')
                            ->label(__('Naziv partnera'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Opis suradnje'))
                            ->rows(7)
                            ->nullable(),

                        Field::image('image')
                            ->label(__('Slika'))
                            ->help(corexis_image_upload()->helpText().' '.__('Za partnere je najbolji logo na svijetloj pozadini.'))
                            ->size('small')
                            ->fit('contain')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),

                        Field::url('url')
                            ->label(__('Web stranica'))
                            ->nullable()
                            ->max(2048),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati partnera?'))
                            ->confirmText(__('Odabrani partner će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled partnera'))
                    ->description(__('Odaberite kako će se partneri prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Siguran prikaz partnera s logotipom, opisom i poveznicom.'))
                            ->option('preview', 'partners_cards'),

                        LayoutVariant::add('logos')
                            ->label(__('Zid podrške'))
                            ->description(__('Čišći logo-prikaz za veći broj partnera i ustanova.'))
                            ->option('preview', 'partners_logos'),

                        LayoutVariant::add('list')
                            ->label(__('Suradnje u redu'))
                            ->description(__('Pregledan popis s više prostora za opis suradnje.'))
                            ->option('preview', 'partners_list'),

                        LayoutVariant::add('featured_list')
                            ->label(__('Istaknuti popis'))
                            ->description(__('Prvi partner je istaknut preko širine, a ostali su raspoređeni u dva stupca.'))
                            ->option('preview', 'partners_featured_list'),
                    ]),
            ]);
    }

    private function statistics(): AdminSection
    {
        return $this->withDemo(AdminSection::add('statistics'), 'statistics')
            ->label(__('Postignuća zadruge'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled brojki je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Postignuća zadruge'))
                    ->description(__('Uredite brojke koje se prikazuju u ovoj sekciji.'))
                    ->formTitle(__('Uredi brojku'), __('Dodaj brojku'))
                    ->addLabel(__('Dodaj brojku'))
                    ->emptyText(__('Nema dodanih brojki.'))
                    ->modalDescription(__('Unesite naziv, vrijednost i ikonu za ovu brojku.'))
                    ->showVisibility(false)
                    ->fields([
                        Field::text('title')
                            ->label(__('Naziv'))
                            ->required()
                            ->max(255),

                        Field::text('meta_value')
                            ->label(__('Vrijednost'))
                            ->nullable()
                            ->max(255),

                        Field::icon('icon')
                            ->label(__('Ikona'))
                            ->nullable()
                            ->max(255)
                            ->option('show_help', true),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati brojku?'))
                            ->confirmText(__('Odabrana brojka će se premjestiti u arhivu. Možete je kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled brojki'))
                    ->description(__('Odaberite kako će se brojke prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Uredne brojke u jednakim karticama, bez startup dojma.'))
                            ->option('preview', 'stats_cards'),

                        LayoutVariant::add('story')
                            ->label(__('Priča kroz brojke'))
                            ->description(__('Topliji prikaz koji povezuje brojke s kratkim opisima rada.'))
                            ->option('preview', 'stats_story'),

                        LayoutVariant::add('ribbon')
                            ->label(__('Traka uspjeha'))
                            ->description(__('Kompaktan vodoravni prikaz koji dobro radi i s više brojki.'))
                            ->option('preview', 'stats_ribbon'),

                        LayoutVariant::add('split_grid')
                            ->label(__('Podijeljena mreža'))
                            ->description(__('Prozračan 2x2 raspored s ikonom i istaknutom brojkom.'))
                            ->option('preview', 'stats_split_grid'),

                        LayoutVariant::add('compact_grid')
                            ->label(__('Sažeta mreža'))
                            ->description(__('Čist prikaz brojki u jednoj povezanoj mreži bez zasebnih kartica.'))
                            ->option('preview', 'stats_compact_grid'),
                    ]),
            ]);
    }

    private function story(string $key, string $label, string $description): AdminSection
    {
        return $this->withDemo(AdminSection::add($key), $key)
            ->label($label)
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled sekcije je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading($label)
                    ->description($description)
                    ->formTitle(__('Uredi stavku'), __('Dodaj stavku'))
                    ->addLabel(__('Dodaj stavku'))
                    ->emptyText(__('Nema dodanih stavki.'))
                    ->modalDescription(__('Unesite podatke za ovu stavku.'))
                    ->fields([
                        Field::text('title')
                            ->label(__('Naziv'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Opis'))
                            ->rows(7)
                            ->nullable(),

                        Field::image('image')
                            ->label(__('Slika'))
                            ->help(corexis_image_upload()->helpText())
                            ->size('small')
                            ->mediaCollection('image')
                            ->storeAsGalleryMedia(),

                        Field::icon('icon')
                            ->label(__('Ikona'))
                            ->nullable()
                            ->max(255),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati stavku?'))
                            ->confirmText(__('Odabrana stavka će se premjestiti u arhivu. Možete je kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite kako će se ova sekcija prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default($key === 'values' ? 'cards' : 'media_right')
                    ->variants($this->storyLayoutVariants($key)),
            ]);
    }

    private function howToOrder(): AdminSection
    {
        return $this->withDemo(AdminSection::add('how_to_order'), 'how_to_order')
            ->label(__('Kako naručiti'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled sekcije je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Kako naručiti'))
                    ->description(__('Uredite korake i upute koje posjetitelju objašnjavaju kako naručiti radove.'))
                    ->formTitle(__('Uredi korak'), __('Dodaj korak'))
                    ->addLabel(__('Dodaj korak'))
                    ->emptyText(__('Nema dodanih koraka.'))
                    ->modalDescription(__('Unesite podatke za ovaj korak.'))
                    ->fields([
                        Field::text('title')
                            ->label(__('Naziv'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Opis'))
                            ->rows(7)
                            ->nullable(),

                        Field::text('meta_value')
                            ->label(__('Tekst'))
                            ->help(__('Kratka oznaka koraka, npr. Korak 1, Korak 2 ili Korak 3.'))
                            ->nullable()
                            ->max(80),

                        Field::icon('icon')
                            ->label(__('Ikona'))
                            ->nullable()
                            ->max(255),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati korak?'))
                            ->confirmText(__('Odabrani korak će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled sekcije'))
                    ->description(__('Odaberite kako će se ova sekcija prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Siguran i uredan prikaz koji dobro radi s različitim brojem stavki.'))
                            ->option('preview', 'order_cards'),

                        LayoutVariant::add('showcase')
                            ->label(__('Istaknuto'))
                            ->description(__('Prva stavka dobiva veći vizualni prostor, ostale je prate u ritmu.'))
                            ->option('preview', 'order_showcase'),

                        LayoutVariant::add('journal')
                            ->label(__('Redovi'))
                            ->description(__('Mirniji prikaz s više prostora za tekst i opis svake stavke.'))
                            ->option('preview', 'order_journal'),
                    ]),
            ]);
    }

    private function socialLinks(): AdminSection
    {
        return $this->withDemo(AdminSection::add('social_links'), 'social_links')
            ->label(__('Društvene mreže'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled društvenih mreža je spremljen.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Društvene mreže'))
                    ->description(__('Dodajte društvene mreže koje želite prikazati na javnoj kontakt stranici.'))
                    ->formTitle(__('Uredi društvenu mrežu'), __('Dodaj društvenu mrežu'))
                    ->addLabel(__('Dodaj društvenu mrežu'))
                    ->emptyText(__('Nema dodanih društvenih mreža.'))
                    ->modalDescription(__('Unesite naziv, URL i odaberite društvenu mrežu.'))
                    ->showVisibility(false)
                    ->fields([
                        Field::text('title')
                            ->label(__('Naziv društvene mreže'))
                            ->required()
                            ->max(255),

                        Field::url('url')
                            ->label(__('URL'))
                            ->nullable()
                            ->max(2048),

                        Field::icon('icon')
                            ->label(__('Ikona društvene mreže'))
                            ->required()
                            ->max(255)
                            ->options($this->socialIconOptions())
                            ->option('picker', false)
                            ->option('show_help', false),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati društvenu mrežu?'))
                            ->confirmText(__('Odabrana društvena mreža će se premjestiti u arhivu. Možete je kasnije vratiti iz Arhive.')),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled društvenih mreža'))
                    ->description(__('Odaberite kako će se poveznice prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('cards')
                    ->variants([
                        LayoutVariant::add('cards')
                            ->label(__('Jednake kartice'))
                            ->description(__('Siguran prikaz s jasnim karticama za svaku društvenu mrežu.'))
                            ->option('preview', 'social_cards'),

                        LayoutVariant::add('strip')
                            ->label(__('Traka poveznica'))
                            ->description(__('Kompaktan horizontalni prikaz koji radi s malo i puno mreža.'))
                            ->option('preview', 'social_strip'),

                        LayoutVariant::add('icons')
                            ->label(__('Ikone'))
                            ->description(__('Minimalan prikaz kada želite naglasiti same ikone.'))
                            ->option('preview', 'social_icons'),
                    ]),
            ]);
    }

    private function contact(): AdminSection
    {
        return $this->withDemo(AdminSection::add('contact'), 'contact')
            ->label(__('Kontakt'))
            ->messages([
                'required' => __('Obavezno polje'),
                'saved' => __('Sekcija je spremljena.'),
                'layout_saved' => __('Izgled kontakta je spremljen.'),
                'settings_saved' => __('Poruka kontakta je spremljena.'),
            ])
            ->tabs([
                Tab::items(__('Sadržaj'))
                    ->heading(__('Kontakt podaci'))
                    ->description(__('Dodajte kontakt podatke koje želite prikazati na javnoj kontakt stranici.'))
                    ->formTitle(__('Uredi kontakt podatak'), __('Dodaj kontakt podatak'))
                    ->addLabel(__('Dodaj kontakt podatak'))
                    ->emptyText(__('Nema dodanih kontakt podataka.'))
                    ->modalDescription(__('Unesite naziv kontakta, vrijednost i ključ ikone.'))
                    ->showVisibility(false)
                    ->fields([
                        Field::text('title')
                            ->label(__('Kontakt'))
                            ->required()
                            ->max(255),

                        Field::textarea('content')
                            ->label(__('Vrijednost'))
                            ->rows(3)
                            ->nullable()
                            ->max(1000),

                        Field::icon('icon')
                            ->label(__('Ikona'))
                            ->nullable()
                            ->max(255)
                            ->option('show_help', true),
                    ])
                    ->actions([
                        Action::edit()
                            ->label(__('Uredi')),

                        Action::delete()
                            ->label(__('Arhiviraj'))
                            ->confirmTitle(__('Arhivirati kontakt podatak?'))
                            ->confirmText(__('Odabrani kontakt podatak će se premjestiti u arhivu. Možete ga kasnije vratiti iz Arhive.')),
                    ]),

                Tab::settings(__('Poruka'), 'message')
                    ->heading(__('Poruka kontakta'))
                    ->description(__('Uredite tekstualni blok koji se prikazuje uz kontakt podatke na javnoj stranici.'))
                    ->fields([
                        Field::text('contact_message_title')
                            ->label(__('Naslov poruke'))
                            ->help(__('Naslov u istaknutom kontakt bloku, npr. Javite nam se.'))
                            ->storage('settings.contact_message_title')
                            ->nullable()
                            ->max(255),

                        Field::textarea('contact_message_description')
                            ->label(__('Uvodni tekst'))
                            ->help(__('Kratka poruka posjetiteljima prije kontakt podataka.'))
                            ->storage('settings.contact_message_description')
                            ->rows(4)
                            ->nullable()
                            ->max(1000),

                        Field::textarea('contact_message_location')
                            ->label(__('Lokacija ili upute'))
                            ->help(__('Dodatna uputa o lokaciji, terminima ili načinu dogovora.'))
                            ->storage('settings.contact_message_location')
                            ->rows(3)
                            ->nullable()
                            ->max(1000),
                    ]),

                Tab::layout(__('Izgled'))
                    ->heading(__('Izgled kontakta'))
                    ->description(__('Odaberite kako će se kontakt podaci prikazati na javnoj stranici.'))
                    ->storage('settings.layout_variant')
                    ->default('split')
                    ->variants([
                        LayoutVariant::add('split')
                            ->label(__('Uravnoteženo'))
                            ->description(__('Kontakt podaci u jasnom dvostupčanom prikazu.'))
                            ->option('preview', 'contact_split'),

                        LayoutVariant::add('cards')
                            ->label(__('Kontakt kartice'))
                            ->description(__('Podaci su razdvojeni u pregledne kartice koje se lako skeniraju.'))
                            ->option('preview', 'contact_cards'),

                        LayoutVariant::add('letter')
                            ->label(__('Topla poruka'))
                            ->description(__('Osobniji prikaz s porukom i podacima u mirnijem ritmu.'))
                            ->option('preview', 'contact_letter'),
                    ]),
            ]);
    }

    private function withDemo(AdminSection $section, string $type): AdminSection
    {
        $demo = $this->sectionDemo($type);

        if (array_key_exists('title', $demo)) {
            $section->option('demo_title', $demo['title']);
        }

        if (array_key_exists('description', $demo)) {
            $section->option('demo_description', $demo['description']);
        }

        if (array_key_exists('settings', $demo)) {
            $section->option('demo_settings', $demo['settings']);
        }

        if (array_key_exists('items', $demo)) {
            $section->option('demo_items', $demo['items']);
        }

        $creatorSummary = $this->sectionCreatorSummary($type);

        if ($creatorSummary !== '') {
            $section->option('creator_summary', $creatorSummary);
        }

        $creatorPanelDescription = $this->sectionCreatorPanelDescription($type);

        if ($creatorPanelDescription !== '') {
            $section->option('creator_panel_description', $creatorPanelDescription);
        }

        $creatorDescription = $this->sectionCreatorDescription($type);

        if ($creatorDescription !== '') {
            $section->option('creator_description', $creatorDescription);
        }

        $creatorExamples = $this->sectionCreatorExamples($type);

        if ($creatorExamples !== []) {
            $section->option('creator_examples', $creatorExamples);
        }

        return $section;
    }

    private function sectionCreatorSummary(string $type): string
    {
        return match ($type) {
            'statistics' => __('Brojevi koji brzo pokazuju opseg rada i aktivnosti.'),
            'about' => __('Uvodna priča o zadruzi, programu ili projektu.'),
            'featured_values' => __('Kratke vrijednosti ili prednosti za vrh stranice.'),
            'content_blocks' => __('Fleksibilni blokovi s naslovom, opisom, slikom i ikonom.'),
            'collaboration' => __('Jedan istaknuti poziv s tekstom, slikom i gumbom.'),
            'features' => '',
            'featured_products' => __('Odabrani radovi iz modula Radovi.'),
            'all_products' => __('Pregled svih javno dostupnih radova i rukotvorina.'),
            'gallery' => __('Nekoliko postojećih albuma iz modula Galerija.'),
            'gallery_grid' => __('Veći pregled postojećih albuma iz modula Galerija.'),
            'photo_gallery' => __('Fotografije koje dodajete izravno u ovu sekciju.'),
            'video' => __('Jedan video za predstavljanje radionice, događaja ili upute.'),
            'calendar' => __('Jednostavan popis radionica, sajmova i školskih događaja.'),
            'testimonials' => __('Kratki dojmovi djece, roditelja, mentora ili partnera.'),
            'partners' => __('Logotipi ili nazivi škola, ustanova, donatora i suradnika.'),
            'latest_news' => __('Pregled objava za stranicu Objave.'),
            'featured_news' => __('Ručno istaknute važne objave ili najave.'),
            'taxonomy_news' => __('Tematski pregled objava prema kategorijama i oznakama.'),
            'faq' => __('Odgovori na česta pitanja prije slanja upita.'),
            'mission' => __('Svrha zadruge i smjer u kojem se želi razvijati.'),
            'vision' => __('Smjer razvoja i dugoročnija slika zadruge.'),
            'team' => __('Predstavljanje mentora, učenika, roditelja ili suradnika.'),
            'values' => __('Načela koja vode rad s djecom i zajednicom.'),
            'how_to_order' => __('Jasni koraci za narudžbu, prijavu ili dogovor.'),
            'contact' => __('Kontakt podaci, adresa i kratke upute za javljanje.'),
            'social_links' => __('Poveznice na društvene mreže i vanjske kanale.'),
            default => '',
        };
    }

    private function sectionCreatorPanelDescription(string $type): string
    {
        return match ($type) {
            'statistics' => __('Brojevima prikažite učenike, radionice, radove, partnere, godine rada ili prikupljene donacije.'),
            'about' => __('Kratka priča: tko ste, kako zadruga radi, zašto postoji i što učenici dobivaju kroz rad.'),
            'featured_values' => __('Tri do četiri kratke poruke za prvi dojam: kreativnost, sigurnost, zajedništvo ili praktično učenje.'),
            'content_blocks' => __('Opća sekcija za vrijednosti, aktivnosti, tim, misiju, prednosti, radionice, projekte, procese, programe, način rada ili kratke sadržajne cjeline. Svaki blok ima naslov, opis, sliku i ikonu.'),
            'collaboration' => __('Koristite za poziv roditeljima, učenicima ili zajednici: uključite se, podržite zadrugu, donirajte materijal, prijavite se na radionicu, posjetite sajam, naručite radove ili dogovorite suradnju.'),
            'features' => __('Kartice za glavne aktivnosti: kreativne radionice, radovi, projekti, galerija, novosti ili suradnje.'),
            'featured_products' => __('Odabrani radovi za naslovnicu ili važan blok: sezonski ukrasi, čestitke, pokloni ili najbolji učenički radovi.'),
            'all_products' => __('Pregled svih radova i rukotvorina za stranicu kataloga, ponude, izložbe ili prodajnog kutka.'),
            'gallery' => __('Prikazuje nekoliko postojećih albuma iz Galerije, npr. radionice, sajmove ili izložbe na naslovnici.'),
            'gallery_grid' => __('Prikazuje veći pregled postojećih albuma iz Galerije. Najbolje za glavnu stranicu Galerija.'),
            'photo_gallery' => __('Fotografije dodajete izravno ovdje, bez stvaranja albuma. Dobro za jednu radionicu, projekt ili događaj.'),
            'video' => __('Jedan video za predstavljanje radionice, upute, izvještaja s događaja, najave ili učeničke prezentacije.'),
            'calendar' => __('Najave radionica, sajmova, izložbi, prodajnih akcija, dana otvorenih vrata i školskih događaja.'),
            'testimonials' => __('Kratki dojmovi učenika, roditelja, mentora ili partnera o radionicama, sajmovima i zajedničkom radu.'),
            'partners' => __('Partneri, donatori, škole, udruge, ustanove i lokalni suradnici koji pomažu prostorom, materijalima ili događajima.'),
            'latest_news' => __('Prikazuje objave iz modula Objave. Najčešće se koristi na stranici Objave kao glavni pregled novosti, najava i izvještaja.'),
            'featured_news' => __('Ručno odabrane objave koje zaslužuju više pažnje: uspjeh učenika, važna najava, izložba ili posebna priča.'),
            'taxonomy_news' => __('Automatski prikazuje objave povezane s kategorijama i oznakama koje odaberete u postavkama sekcije.'),
            'faq' => __('Česta pitanja o prijavama, materijalima, donacijama, narudžbama, terminima, radionicama ili preuzimanju radova.'),
            'mission' => __('Dva kratka zapisa: zašto zadruga postoji i kamo se želi razvijati. Dobro za stranicu O zadruzi.'),
            'vision' => __('Kratka poruka o smjeru razvoja zadruge.'),
            'team' => __('Mentori, učitelji, učenici, roditelji, volonteri ili skupine koje vode radionice i pomažu radu zadruge.'),
            'values' => __('Načela rada: kreativnost, odgovornost, sigurnost, strpljenje, suradnja, održivost ili briga za zajednicu.'),
            'how_to_order' => __('Koraci koje posjetitelj treba pratiti: naručiti rad, poslati upit, rezervirati termin, prijaviti dijete ili dogovoriti preuzimanje.'),
            'contact' => __('Kontakt podaci i upute: kome se javiti, kada, gdje se nalazite i za koje teme primate upite.'),
            'social_links' => __('Poveznice na Facebook, Instagram, YouTube ili druge kanale gdje objavljujete radove, događaje i novosti.'),
            default => '',
        };
    }

    private function sectionCreatorDescription(string $type): string
    {
        return match ($type) {
            'statistics' => __('Koristite za brzi dokaz aktivnosti i povjerenja: broj učenika, radionica, izrađenih radova, partnera, donacija ili godina rada. Dobro funkcionira kada posjetitelj treba u nekoliko sekundi shvatiti opseg i ozbiljnost udruge.'),
            'about' => __('Koristite za osnovnu priču o udruzi ili zadruzi: tko ste, kako radite, kome pomažete i zašto je vaš rad važan. Ovo je dobar uvodni blok za stranice o udruzi, programu ili projektu.'),
            'featured_values' => __('Kratki blok ispod zaglavlja za prikaz najvažnijih vrijednosti, prednosti ili poruka. Dobro radi odmah nakon hero dijela kao miran uvod u sadržaj stranice.'),
            'content_blocks' => __('Koristite kada imate više kratkih sadržajnih blokova, a niste sigurni treba li vam sekcija za vrijednosti, aktivnosti, tim, misiju, radionice, projekte, procese, prednosti ili nešto slično. Svaka stavka ima naslov, opis, sliku i ikonu, a izgled birate naknadno prema tome želite li kartice, redove, istaknuti prikaz ili urednički niz.'),
            'collaboration' => __('Jedan istaknuti poziv s tekstom, slikom i gumbom. Može biti poziv na uključivanje, podršku zadruzi, donaciju materijala, prijavu na radionicu, posjet sajmu, narudžbu radova, prijedlog ideje, gostovanje ili dogovor suradnje.'),
            'features' => __('Svaka stavka prikazuje jedan zapis s nazivom, opisom, slikom i ikonom.'),
            'featured_products' => __('Koristite kada želite na naslovnici ili važnoj stranici istaknuti nekoliko odabranih radova iz kataloga. Sekcija ne traži ručni unos radova, nego povlači postojeće radove koje već uređujete u modulu Radovi.'),
            'all_products' => __('Koristite za stranicu kataloga ili pregled svih javno dostupnih radova. Posjetitelj dobiva uredan popis radova, a vi sadržaj i slike i dalje održavate na jednom mjestu u modulu Radovi.'),
            'gallery' => __('Koristite kada želite na naslovnici ili važnoj stranici prikazati nekoliko postojećih albuma iz modula Galerija. Fotografije se ne uređuju u ovoj sekciji, nego u Galeriji.'),
            'gallery_grid' => __('Koristite za glavnu stranicu galerije ili veći pregled postojećih albuma. Posjetitelj dobiva jasan ulaz u radionice, izložbe, pripreme, sajmove i školske aktivnosti.'),
            'photo_gallery' => __('Koristite kada želite ručno dodati nekoliko fotografija baš u ovu sekciju, bez otvaranja novog albuma. Dobro za jednu radionicu, projekt, gostovanje, događaj ili vizualni dodatak tekstu.'),
            'video' => __('Koristite za YouTube video koji posjetitelj može otvoriti u modalu bez odlaska sa stranice. Dobar je za predstavljanje radionice, upute, kratki izvještaj, promotivni video ili snimku događanja.'),
            'calendar' => __('Koristite za najave radionica, izložbi, sajmova, prodajnih akcija, školskih događaja i drugih aktivnosti zadruge. Sekcija prikazuje jednostavan popis nadolazećih događaja bez složenog mjesečnog kalendara.'),
            'testimonials' => __('Koristite za dojmove djece, roditelja, mentora, partnera ili korisnika. Kratki citati pomažu da stranica ne zvuči samo informativno, nego pokazuje kako ljudi doživljavaju vaš rad.'),
            'partners' => __('Koristite za škole, ustanove, donatore, sponzore, udruge i lokalne suradnike. Sekcija pomaže pokazati mrežu podrške, zahvaliti partnerima i posjetitelju dati sigurnost da iza programa stoji zajednica.'),
            'latest_news' => __('Koristite za glavnu stranicu Objave ili veći pregled novosti, najava, izvještaja, uspjeha učenika i školskih obavijesti. Sekcija povlači objave koje uređujete u modulu Objave.'),
            'featured_news' => __('Koristite kada ne želite prikazati samo najnovije objave, nego posebno istaknuti važne priče, najave, uspjehe ili obavijesti. Dobro radi na naslovnici kao ulaz u sadržaj koji treba veću pažnju.'),
            'taxonomy_news' => __('Koristite za tematske cjeline koje se automatski pune objavama, primjerice radionice, događaji, uspjesi učenika ili projekti. Odaberite željene kategorije i oznake, a nove povezane objave pojavit će se bez dodatnog uređivanja sekcije.'),
            'faq' => __('Koristite za pitanja koja se stalno ponavljaju: prijave, cijene, termini, donacije, suradnje, preuzimanje radova ili pravila sudjelovanja. Sekcija smanjuje nejasnoće prije nego korisnik pošalje upit.'),
            'mission' => __('Sekcija spaja misiju i viziju u dva kratka zapisa: zašto zadruga postoji i kamo se želi razvijati.'),
            'vision' => __('Koristite za smjer razvoja: kamo želite doći, kakvu zajednicu gradite i što želite omogućiti kroz radionice, proizvode ili suradnje. Dobra je za stranice koje trebaju pokazati dugoročniju sliku.'),
            'team' => __('Koristite za predstavljanje ljudi ili skupina iza rada: mentora, voditelja, volontera, učenika, roditelja ili suradnika. Sekcija pomaže posjetitelju vidjeti tko stoji iza aktivnosti i kome se obraća.'),
            'values' => __('Koristite za načela koja vode vaš rad: kreativnost, sigurnost, suradnja, odgovornost, tradicija, održivost ili uključivost. Dobro funkcionira kada želite objasniti ne samo što radite, nego kako radite.'),
            'how_to_order' => __('Koristite za jasne korake kada posjetitelj treba nešto napraviti: naručiti rad, rezervirati termin, prijaviti dijete, poslati upit ili dogovoriti suradnju. Svaka stavka objašnjava jedan korak i smanjuje nesigurnost.'),
            'contact' => __('Koristite za glavne kontakt podatke i kratke upute: kome se javiti, gdje se nalazite, kada je najbolje poslati poruku i za koje teme vas ljudi mogu kontaktirati.'),
            'social_links' => __('Koristite za poveznice na društvene mreže i kanale gdje redovito objavljujete novosti. Dobro je staviti pri dnu kontakt stranice ili na mjestu gdje posjetitelja želite zadržati u daljnjoj komunikaciji.'),
            default => '',
        };
    }

    /** @return array<int, string> */
    private function sectionCreatorExamples(string $type): array
    {
        return match ($type) {
            'statistics' => [
                __('120 učenika sudjelovalo u radionicama tijekom godine.'),
                __('35 izrađenih radova, 12 aktivnosti i 8 partnera.'),
                __('Kratki blok odmah nakon uvoda na naslovnici.'),
            ],
            'about' => [
                __('Tko vodi zadrugu i kako učenici sudjeluju u radu.'),
                __('Kratka priča o nastanku školskog projekta.'),
                __('Uvodni blok na stranici "O zadruzi".'),
            ],
            'featured_values' => [
                __('Tri razloga zašto je zadruga važna za učenike.'),
                __('Kreativnost kroz rad rukama, zajedništvo u grupi i praktično učenje kroz stvarne zadatke.'),
                __('Vrijednosti koje želite odmah istaknuti roditeljima: sigurnost, suradnja i ponos učenika na vlastiti rad.'),
                __('Kratki uvod na naslovnici koji posjetitelju odmah objašnjava što djeca dobivaju kroz aktivnosti zadruge.'),
            ],
            'content_blocks' => [
                __('Vrijednosti, aktivnosti, prednosti ili koraci prikazani kroz isti tip bloka.'),
                __('Tim, mentori, volonteri ili skupine koje želite predstaviti.'),
                __('Misija i vizija, način rada ili proces stvaranja kroz nekoliko kratkih zapisa.'),
                __('Radionice, projekti, programi ili usluge koje želite objasniti bez otvaranja posebne sekcije.'),
                __('Jedna fleksibilna sekcija kada ne želite birati između nekoliko sličnih sekcija.'),
            ],
            'collaboration' => [
                __('Uključite se u rad zadruge.'),
                __('Podržite zadrugu donacijom materijala ili opreme.'),
                __('Prijavite se na radionicu ili posjetite školski sajam.'),
                __('Naručite radove, predložite ideju ili dogovorite gostovanje.'),
            ],
            'features' => [
                __('Kreativne radionice'),
                __('Radovi i proizvodi'),
                __('Projekti i aktivnosti'),
                __('Galerija radova'),
                __('Novosti i najave'),
                __('Suradnja s partnerima'),
            ],
            'featured_products' => [
                __('Tri do šest radova koje želite posebno istaknuti.'),
                __('Sezonske rukotvorine, čestitke ili ukrasi.'),
                __('Ulaz u katalog radova s naslovnice.'),
            ],
            'all_products' => [
                __('Glavna stranica "Radovi i rukotvorine".'),
                __('Pregled svih javno objavljenih učeničkih radova.'),
                __('Katalog proizvoda koji se uređuje u modulu Radovi.'),
            ],
            'gallery' => [
                __('Nekoliko postojećih albuma na naslovnici.'),
                __('Radionice, sajmovi ili izložbe iz modula Galerija.'),
                __('Kratak vizualni ulaz u atmosferu rada zadruge.'),
            ],
            'gallery_grid' => [
                __('Glavna stranica sa svim albumima.'),
                __('Veći pregled radionica, sajmova i izložbi.'),
                __('Jasan ulaz u postojeće albume iz Galerije.'),
            ],
            'photo_gallery' => [
                __('Nekoliko ručno dodanih fotografija u ovoj sekciji.'),
                __('Slike za jednu radionicu, projekt ili događaj.'),
                __('Dobar izbor kada ne želite stvarati novi album.'),
            ],
            'video' => [
                __('YouTube video o radionici ili predstavljanju radova.'),
                __('Kratke upute za izradu jednostavne rukotvorine.'),
                __('Snimka školskog događaja ili izložbe.'),
            ],
            'calendar' => [
                __('Radionica izrade ukrasa s datumom, vremenom i lokacijom.'),
                __('Školski sajam, izložba ili prodajna akcija.'),
                __('Dani otvorenih vrata i kreativne aktivnosti.'),
            ],
            'testimonials' => [
                __('Izjava učenika nakon radionice.'),
                __('Kratak dojam roditelja ili mentora.'),
                __('Nekoliko citata koji daju stranici ljudski ton.'),
            ],
            'partners' => [
                __('Škola, grad, donatori i lokalni suradnici.'),
                __('Ustanove koje pomažu opremom, prostorom ili materijalom.'),
                __('Zahvala partnerima pri dnu stranice.'),
            ],
            'latest_news' => [
                __('Glavna stranica Objave.'),
                __('Pregled novosti, najava, izvještaja i školskih obavijesti.'),
                __('Veći popis objava s gumbom Prikaži više.'),
            ],
            'featured_news' => [
                __('Posebno važna najava ili uspjeh učenika.'),
                __('Odabrane objave koje trebaju veću pažnju.'),
                __('Istaknuta priča na naslovnici.'),
            ],
            'taxonomy_news' => [
                __('Sve objave iz kategorije Radionice.'),
                __('Objave označene kao Uspjesi učenika ili Projekti.'),
                __('Tematski blok koji se automatski nadopunjuje novim objavama.'),
            ],
            'faq' => [
                __('Kako se prijaviti na radionicu?'),
                __('Jesu li aktivnosti besplatne?'),
                __('Može li se donirati materijal ili dogovoriti suradnja?'),
            ],
            'mission' => [
                __('Misija: zašto zadruga postoji.'),
                __('Vizija: kamo se zadruga želi razvijati.'),
                __('Dobar blok za stranicu "O zadruzi".'),
            ],
            'vision' => [
                __('Kakvu zajednicu želite graditi kroz radionice.'),
                __('Što želite razvijati kroz idućih nekoliko godina.'),
                __('Smjer koji povezuje učenike, roditelje i lokalnu zajednicu.'),
            ],
            'team' => [
                __('Mentori i učitelji koji vode radionice.'),
                __('Učenici, roditelji i volonteri koji sudjeluju u radu.'),
                __('Suradnici koji pomažu oko materijala, prostora ili izložbi.'),
            ],
            'values' => [
                __('Kreativnost, sigurnost, suradnja i odgovornost.'),
                __('Način rada s djecom, a ne samo popis aktivnosti.'),
                __('Vrijednosti koje želite istaknuti roditeljima i partnerima.'),
            ],
            'how_to_order' => [
                __('Pošaljite upit, dogovorite detalje, preuzmite rad.'),
                __('Koraci za prijavu na radionicu ili rezervaciju termina.'),
                __('Jednostavne upute kada posjetitelj treba nešto napraviti.'),
            ],
            'contact' => [
                __('E-pošta, telefon, adresa i osoba za kontakt.'),
                __('Kada se javiti za radionice, suradnje ili donacije.'),
                __('Kontakt blok na kraju stranice.'),
            ],
            'social_links' => [
                __('Facebook i Instagram profili zadruge.'),
                __('Vanjske poveznice na kanale gdje objavljujete novosti.'),
                __('Kratak blok pri dnu stranice ili u kontakt dijelu.'),
            ],
            default => [],
        };
    }

    /** @return array<string, mixed> */
    private function sectionDemo(string $type): array
    {
        return match ($type) {
            'statistics' => [
                'title' => __('Postignuća zadruge'),
                'description' => __('Mali pregled radionica, radova i suradnji koje su nastale kroz dječji trud i zajednički rad.'),
                'settings' => ['layout_variant' => 'cards', 'show_title' => false, 'show_description' => false],
                'items' => [
                    ['title' => __('Učenika i mladih'), 'icon' => 'users', 'settings' => ['value' => '120+']],
                    ['title' => __('Provedenih aktivnosti'), 'icon' => 'folder', 'settings' => ['value' => '12']],
                    ['title' => __('Radova i rukotvorina'), 'icon' => 'cube', 'settings' => ['value' => '35']],
                    ['title' => __('Partnera i suradnika'), 'icon' => 'building-office', 'settings' => ['value' => '8']],
                ],
            ],
            'about' => [
                'title' => __('O zadruzi'),
                'description' => __('Ovo je prostor u kojem učenici uče stvarati, surađivati i predstavljati svoje radove zajednici.'),
                'settings' => ['layout_variant' => 'diagonal', 'show_title' => true, 'show_description' => true],
                'items' => [
                    [
                        'title' => __('O zadruzi'),
                        'content' => __('Školska zadruga okuplja učenike, učitelje, roditelje i suradnike s ciljem poticanja kreativnosti, odgovornosti i zajedničkog rada. Kroz različite radionice i projekte učenici razvijaju praktične vještine, stvaralačko razmišljanje i poduzetnički duh.'),
                    ],
                ],
            ],
            'featured_values' => [
                'title' => __('Istaknute vrijednosti'),
                'description' => __('Kratki blok ispod zaglavlja za prikaz najvažnijih vrijednosti, prednosti ili poruka.'),
                'settings' => ['layout_variant' => 'columns_3', 'show_title' => false, 'show_description' => false],
                'items' => [
                    ['title' => __('Kreativne radionice'), 'content' => __('Radionice u kojima učimo, stvaramo i razvijamo vještine.'), 'icon' => 'sparkles'],
                    ['title' => __('Rukotvorine'), 'content' => __('Unikatni proizvodi nastali kreativnim rukama naših učenika.'), 'icon' => 'gift'],
                    ['title' => __('Zajedništvo'), 'content' => __('Suradnjom, prijateljstvom i zajedničkim radom gradimo bolju zajednicu.'), 'icon' => 'users'],
                ],
            ],
            'content_blocks' => [
                'title' => __('Sadržajni blokovi'),
                'description' => __('Jedna fleksibilna sekcija za vrijednosti, aktivnosti, tim, misiju, prednosti, radionice, projekte, procese ili kratke sadržajne cjeline.'),
                'settings' => ['layout_variant' => 'mosaic', 'show_title' => true, 'show_description' => true],
                'items' => [
                    ['title' => __('Kreativnost i briga'), 'content' => __('Njegujemo ideje, suradnju i pažljiv rad od prve skice do gotovog rezultata.'), 'icon' => 'sparkles'],
                    ['title' => __('Rad kroz praksu'), 'content' => __('Učenici uče istraživanjem materijala, izradom predmeta i predstavljanjem svojih radova.'), 'icon' => 'cube'],
                    ['title' => __('Zajedništvo'), 'content' => __('Povezujemo učenike, mentore, roditelje i lokalnu zajednicu kroz konkretne aktivnosti.'), 'icon' => 'users'],
                ],
            ],
            'collaboration' => [
                'title' => __('Uključite se'),
                'description' => __('Jedan istaknuti poziv za roditelje, partnere i zajednicu.'),
                'settings' => ['show_title' => false, 'show_description' => false, 'layout_variant' => 'banner', 'show_button' => true, 'button_text' => __('Javite nam se')],
                'items' => [
                    [
                        'title' => __('Postanite dio naše priče'),
                        'content' => __('Rado primamo pomoć roditelja, škole, lokalnih ustanova i svih koji žele podržati radionice, materijale, sajmove i radove naših učenika.'),
                    ],
                ],
            ],
            'features' => [
                'title' => __('Naše aktivnosti'),
                'description' => __('Kartice koje posjetiteljima brzo pokazuju što zadruga radi i koje su njezine glavne aktivnosti.'),
                'settings' => ['layout_variant' => 'mosaic'],
                'items' => [
                    ['title' => __('Kreativne radionice'), 'content' => __('Učimo kroz izradu, istraživanje materijala i stvaranje uporabnih predmeta.'), 'icon' => 'sparkles'],
                    ['title' => __('Radovi i proizvodi'), 'content' => __('Predstavljamo rukotvorine, ukrase i druge radove nastale u zadruzi.'), 'icon' => 'cube'],
                    ['title' => __('Projekti i aktivnosti'), 'content' => __('Sudjelujemo u školskim i lokalnim aktivnostima tijekom godine.'), 'icon' => 'folder'],
                    ['title' => __('Galerija radova'), 'content' => __('Bilježimo trenutke iz radionica, izložbi i predstavljanja.'), 'icon' => 'photo'],
                    ['title' => __('Novosti i najave'), 'content' => __('Objavljujemo informacije o radionicama, događajima i uspjesima.'), 'icon' => 'newspaper'],
                    ['title' => __('Suradnja s partnerima'), 'content' => __('Povezujemo školu, roditelje, lokalnu zajednicu i ustanove.'), 'icon' => 'users'],
                ],
            ],
            'featured_products' => [
                'title' => __('Istaknuti radovi'),
                'description' => __('Odabrani radovi i rukotvorine koji najbolje predstavljaju kreativnost članova.'),
                'settings' => ['show_price' => true, 'limit' => 3, 'layout_variant' => 'highlighted', 'single_back_label' => __('Natrag na radove'), 'single_previous_label' => __('Prethodni rad'), 'single_next_label' => __('Sljedeći rad')],
            ],
            'all_products' => [
                'title' => __('Radovi i rukotvorine'),
                'description' => __('Pregled radova nastalih kroz radionice, školske aktivnosti i zajednički kreativni rad.'),
                'settings' => ['show_price' => true, 'limit' => 3, 'layout_variant' => 'cards', 'single_back_label' => __('Natrag na radove'), 'single_previous_label' => __('Prethodni rad'), 'single_next_label' => __('Sljedeći rad')],
            ],
            'gallery' => [
                'title' => __('Istaknuti albumi'),
                'description' => __('Pogled u radionice, druženja, izložbe i male kreativne procese koji čine svakodnevicu Pletenice.'),
                'settings' => ['layout_variant' => 'wall', 'limit' => 3],
            ],
            'gallery_grid' => [
                'title' => __('Svi albumi'),
                'description' => __('Pregled albuma iz radionica, druženja, priprema, sajmova i predstavljanja radova.'),
                'settings' => ['layout_variant' => 'cards', 'limit' => 3],
            ],
            'photo_gallery' => [
                'title' => __('Fotografije u sekciji'),
                'description' => __('Dodajte nekoliko fotografija koje najbolje prikazuju ovu radionicu, projekt ili događaj.'),
                'settings' => ['layout_variant' => 'grid', 'limit' => 8, 'image_ratio' => 'four_three', 'show_captions' => false],
            ],
            'video' => [
                'title' => __('Video'),
                'description' => __('Pogledajte kratki video o našim aktivnostima, radionicama ili radovima.'),
                'settings' => ['layout_variant' => 'cards'],
            ],
            'calendar' => [
                'title' => __('Kalendar događaja'),
                'description' => __('Pratite radionice, izložbe i aktivnosti koje pripremamo kroz školsku godinu.'),
                'settings' => ['layout_variant' => 'calendar-split'],
                'items' => [
                    [
                        'title' => __('Radionica izrade ukrasa'),
                        'content' => __('Učenici izrađuju male dekoracije i pripremaju radove za školski prodajni kutak.'),
                        'settings' => [
                            'event_date' => now()->addDays(10)->toDateString(),
                            'starts_at' => '09:00',
                            'ends_at' => '12:00',
                            'location' => __('Učionica likovne kulture'),
                        ],
                    ],
                    [
                        'title' => __('Izložba radova zadruge'),
                        'content' => __('Predstavljamo odabrane radove nastale kroz radionice i školske aktivnosti.'),
                        'settings' => [
                            'event_date' => now()->addDays(18)->toDateString(),
                            'starts_at' => '17:00',
                            'location' => __('Školski hol'),
                        ],
                    ],
                    [
                        'title' => __('Školski sajam'),
                        'content' => __('Prodajna akcija učeničkih radova uz druženje roditelja, učenika i suradnika.'),
                        'settings' => [
                            'event_date' => now()->addDays(28)->toDateString(),
                            'starts_at' => '10:00',
                            'ends_at' => '13:00',
                            'location' => __('Školsko dvorište'),
                        ],
                    ],
                ],
            ],
            'testimonials' => [
                'title' => __('Dojmovi iz zadruge'),
                'description' => __('Kratke izjave djece i mentora o radionicama, stvaranju i zajedničkom radu.'),
                'settings' => ['layout_variant' => 'notes'],
                'items' => [
                    ['title' => __('Mia'), 'subtitle' => __('učenica, kreativna radionica'), 'content' => __('Najviše volim kad od malih komadića napravimo nešto što možemo pokazati drugima.')],
                    ['title' => __('Luka'), 'subtitle' => __('učenik, izrada rukotvorina'), 'content' => __('Na radionici sam naučio da ne mora sve uspjeti odmah. Kad popraviš rad, bude još bolji.')],
                    ['title' => __('Mentorica Ana'), 'subtitle' => __('voditeljica radionice'), 'content' => __('Djeca se najviše otvore kad imaju siguran prostor za ideje, razgovor i rad vlastitim rukama.')],
                ],
            ],
            'partners' => [
                'title' => __('Partneri'),
                'description' => __('Partneri koji pomažu u provedbi kreativnih aktivnosti, radionica i programa za djecu i mlade.'),
                'settings' => ['layout_variant' => 'cards'],
                'items' => [
                    ['title' => __('Ustanova'), 'content' => __('Prostor, podrška i suradnja u provedbi kreativnih radionica i školskih aktivnosti.'), 'url' => null],
                    ['title' => __('Lokalna zajednica'), 'content' => __('Podrška lokalnim programima koji potiču kreativnost, zajedništvo i rad s djecom i mladima.'), 'url' => null],
                    ['title' => __('Suradnici'), 'content' => __('Roditelji, volonteri i kreativci koji sudjeluju u radionicama, događanjima i izložbama.')],
                ],
            ],
            'latest_news' => [
                'title' => __('Sve objave'),
                'description' => __('Pregled novosti, najava, izvještaja i aktivnosti koje provodimo kroz školsku godinu.'),
                'settings' => ['limit' => 12, 'layout_variant' => 'cards', 'read_more_label' => __('Pročitaj objavu'), 'load_more_label' => __('Prikaži više'), 'single_previous_label' => __('Prethodna objava'), 'single_next_label' => __('Sljedeća objava'), 'show_author' => true, 'show_date' => true],
            ],
            'featured_news' => [
                'title' => __('Istaknute objave'),
                'description' => __('Odabrane priče, najave i aktivnosti koje želite posebno istaknuti na stranici.'),
                'settings' => ['limit' => 3, 'layout_variant' => 'featured', 'read_more_label' => __('Pročitaj objavu'), 'load_more_label' => __('Prikaži više'), 'single_previous_label' => __('Prethodna objava'), 'single_next_label' => __('Sljedeća objava'), 'show_author' => false, 'show_date' => false],
            ],
            'taxonomy_news' => [
                'title' => __('Tematske objave'),
                'description' => __('Objave povezane s odabranim kategorijama i oznakama.'),
                'settings' => ['limit' => 6, 'layout_variant' => 'cards', 'taxonomy_item_uuids' => [], 'read_more_label' => __('Pročitaj objavu'), 'load_more_label' => __('Prikaži više'), 'single_previous_label' => __('Prethodna objava'), 'single_next_label' => __('Sljedeća objava'), 'show_author' => true, 'show_date' => true],
            ],
            'faq' => [
                'title' => __('Česta pitanja'),
                'description' => __('Najvažnije informacije za roditelje, članove, partnere i sve koji žele sudjelovati.'),
                'settings' => ['layout_variant' => 'answer_grid'],
                'items' => [
                    ['title' => __('Tko se može uključiti u aktivnosti?'), 'content' => __('Aktivnosti su prvenstveno namijenjene djeci i mladima, uz podršku roditelja, škole, volontera i lokalnih suradnika.')],
                    ['title' => __('Jesu li aktivnosti besplatne?'), 'content' => __('Dio aktivnosti provodi se uz podršku partnera i donatora, a za pojedine radionice moguće je sudjelovanje u troškovima materijala.')],
                    ['title' => __('Kako se prijaviti na radionicu?'), 'content' => __('Najjednostavnije je javiti se putem kontakt obrasca ili izravno organizatorima u školi. Nakon toga šaljemo detalje termina i programa.')],
                ],
            ],
            'mission' => [
                'title' => __('Misija i vizija'),
                'description' => __('Kratko objašnjenje zašto zadruga postoji i u kojem smjeru želi razvijati učenike, radionice i suradnju.'),
                'settings' => ['layout_variant' => 'cards', 'show_title' => true, 'show_description' => true],
                'items' => [
                    ['title' => __('Misija'), 'content' => __('Učenicima dajemo prostor da kroz rad rukama, pokušaje i suradnju razvijaju vještine, strpljenje i ponos na vlastiti trud.'), 'icon' => 'sparkles'],
                    ['title' => __('Vizija'), 'content' => __('Želimo da zadruga postane mjesto koje povezuje učenike, školu, roditelje i lokalnu zajednicu kroz korisne radove i zajedničke aktivnosti.'), 'icon' => 'sun'],
                ],
            ],
            'vision' => [
                'title' => __('Vizija'),
                'description' => __('Želimo da radionice, izložbe i mali proizvodi povežu učenike, roditelje, školu i lokalnu zajednicu.'),
                'settings' => ['layout_variant' => 'media_right', 'show_title' => false, 'show_description' => false],
                'items' => [
                    ['title' => __('Zajednica koja raste'), 'content' => __('Naša vizija je razvijati programe koji djeci i mladima daju samopouzdanje, vještine i osjećaj pripadnosti.'), 'icon' => 'sun'],
                ],
            ],
            'team' => [
                'title' => __('Tim'),
                'description' => __('Iza Pletenice stoje učitelji, roditelji, volonteri i suradnici koji pripremaju radionice i prate dječje ideje od prvog pokušaja do gotovog rada.'),
                'settings' => ['layout_variant' => 'path'],
                'items' => [
                    ['title' => __('Mentori i suradnici'), 'content' => __('Tim čine osobe koje organiziraju radionice, pripremaju materijale, prate rad djece i povezuju udrugu s partnerima.'), 'icon' => 'users'],
                ],
            ],
            'values' => [
                'title' => __('Vrijednosti'),
                'description' => __('U radu s djecom najvažniji su nam sigurnost, mašta, strpljenje i osjećaj da svaki trud vrijedi.'),
                'settings' => ['layout_variant' => 'cards'],
                'items' => [
                    ['title' => __('Kreativnost i briga'), 'content' => __('Njegujemo osobni izraz i hrabrost da se ideja pretvori u konkretan rad.'), 'icon' => 'heart'],
                    ['title' => __('Strpljenje i pokušaji'), 'content' => __('Djeca imaju prostor isprobati ideju, pogriješiti, popraviti i naučiti da je svaki korak dio stvaranja.'), 'icon' => 'arrow-path'],
                    ['title' => __('Suradnja i poštovanje'), 'content' => __('Radionice potiču pomaganje, slušanje i dogovor, jer dobar rad nastaje kada se djeca osjećaju sigurno i prihvaćeno.'), 'icon' => 'users'],
                ],
            ],
            'how_to_order' => [
                'title' => __('Kako naručiti ili rezervirati rad'),
                'description' => __('Ako vam se sviđa neki rad ili želite podržati aktivnosti udruge, javite nam se s upitom.'),
                'settings' => ['layout_variant' => 'journal'],
                'items' => [
                    ['title' => __('Pošaljite upit'), 'content' => __('Napišite koji rad vas zanima, za koju prigodu i treba li vam dodatna prilagodba.'), 'icon' => 'chat-bubble-left-right', 'settings' => ['value' => __('Korak 1')]],
                    ['title' => __('Dogovorimo detalje'), 'content' => __('Ako trebate veću količinu ili poseban motiv, javite nam što vam treba pa ćemo provjeriti mogućnosti izrade.'), 'icon' => 'chat-bubble-left-right', 'settings' => ['value' => __('Korak 2')]],
                    ['title' => __('Preuzmite radove'), 'content' => __('Nakon dogovora radove možete preuzeti u školi ili na dogovorenom događanju, izložbi ili prodajnom kutku.'), 'icon' => 'gift', 'settings' => ['value' => __('Korak 3')]],
                ],
            ],
            'contact' => [
                'title' => __('Kontakt'),
                'description' => __('Za radionice, suradnje, donacije materijala ili pitanja javite nam se porukom.'),
                'settings' => [
                    'layout_variant' => 'letter',
                    'type' => 'contact',
                    'contact_message_title' => __('Javite nam se'),
                    'contact_message_description' => __('Rado primamo upite roditelja, učenika, suradnika i partnera koji žele podržati kreativne aktivnosti za djecu i mlade.'),
                    'contact_message_location' => __('Aktivnosti se provode prema dogovoru s voditeljima.'),
                ],
                'items' => [
                    ['title' => __('Udruga'), 'content' => __('Naziv udruge'), 'icon' => 'building-office-2'],
                    ['title' => __('E-pošta'), 'content' => null, 'icon' => 'envelope'],
                    ['title' => __('Telefon'), 'content' => null, 'icon' => 'phone'],
                ],
            ],
            'social_links' => [
                'title' => __('Društvene mreže'),
                'description' => __('Pratite nas na društvenim mrežama i pogledajte najnovije aktivnosti, radionice i radove.'),
                'settings' => ['type' => 'social_links', 'layout_variant' => 'icons'],
                'items' => [
                    ['title' => 'Facebook', 'icon' => 'facebook', 'url' => null],
                    ['title' => 'Instagram', 'icon' => 'instagram', 'url' => null],
                    ['title' => 'YouTube', 'icon' => 'youtube', 'url' => null],
                ],
            ],
            default => [],
        };
    }

    /** @return array<string, string> */
    private function socialIconOptions(): array
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'linkedin' => 'LinkedIn',
            'x' => 'X / Twitter',
            'whatsapp' => 'WhatsApp',
            'viber' => 'Viber',
            'pinterest' => 'Pinterest',
        ];
    }

    /** @return array<int, LayoutVariant> */
    private function storyLayoutVariants(string $key): array
    {
        $variants = [];

        if ($key !== 'values') {
            $variants = [
                LayoutVariant::add('media_right')
                    ->label(__('Tekst lijevo'))
                    ->description(__('Tekst je lijevo i poravnat po sredini fotografije koja stoji desno.'))
                    ->option('preview', 'story_media_right'),

                LayoutVariant::add('media_left')
                    ->label(__('Slika lijevo'))
                    ->description(__('Fotografija je lijevo, a tekst desno u istom mirnom ritmu.'))
                    ->option('preview', 'story_media_left'),
            ];
        }

        return [
            ...$variants,

            LayoutVariant::add('path')
                ->label(__('Put stvaranja'))
                ->description(__('Naizmjenični prikaz slike i teksta prema izgledu područja rada.'))
                ->option('preview', 'story_path'),

            LayoutVariant::add('cards')
                ->label(__('Jednake kartice'))
                ->description(__('Siguran i uredan prikaz koji dobro radi s različitim brojem stavki.'))
                ->option('preview', 'story_cards'),

            LayoutVariant::add('showcase')
                ->label(__('Istaknuto'))
                ->description(__('Prva stavka dobiva veći vizualni prostor, ostale je prate u ritmu.'))
                ->option('preview', 'story_showcase'),

            LayoutVariant::add('journal')
                ->label(__('Redovi'))
                ->description(__('Mirniji prikaz s više prostora za tekst i opis svake stavke.'))
                ->option('preview', 'story_journal'),
        ];
    }
}
