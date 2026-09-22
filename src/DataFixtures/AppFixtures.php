<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    private function generateRandomDate(): DateTimeImmutable
    {
        $start = new DateTimeImmutable('2025-09-01');
        $end = new DateTimeImmutable('2025-09-20');
        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        return (new DateTimeImmutable())->setTimestamp($randomTimestamp);
    }

    private function generateRandomEan13(): string
    {
        $ean13 = '';
        for ($i = 0; $i < 13; $i++) {
            $ean13 .= mt_rand(0, 9);
        }
        return $ean13;
    }

    private function generateLoremIpsum(int $wordCount): string
    {
        $words = [
            "lorem", "ipsum", "dolor", "sit", "amet", "consectetur", "adipiscing", "elit", "sed", "do",
            "eiusmod", "tempor", "incididunt", "ut", "labore", "et", "dolore", "magna", "aliqua", "ut",
            "enim", "ad", "minim", "veniam", "quis", "nostrud", "exercitation", "ullamco", "laboris", "nisi",
            "ut", "aliquip", "ex", "ea", "commodo", "consequat", "duis", "aute", "irure", "dolor",
            "in", "reprehenderit", "in", "voluptate", "velit", "esse", "cillum", "dolore", "eu", "fugiat",
            "nulla", "pariatur", "excepteur", "sint", "occaecat", "cupidatat", "non", "proident", "sunt", "in",
            "culpa", "qui", "officia", "deserunt", "mollit", "anim", "id", "est", "laborum"
        ];
        $lorem = [];
        for ($i = 0; $i < $wordCount; $i++) {
            $lorem[] = $words[array_rand($words)];
        }
        return ucfirst(implode(' ', $lorem)) . '.';
    }

    private function generateRandomSku(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return 'REF' . $randomString;
    }

    private function generateSlug(int $id, string $text): string
    {
        // 1. Convertit les caractères accentués en leur équivalent ASCII (ex: é -> e, à -> a, ç -> c)
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        // // 2. Passe l'ensemble de la chaîne en minuscules
        $text = strtolower($text);

        // // 3. Remplace tout ce qui n'est pas une lettre ou un chiffre (espaces, apostrophes, tirets...) par un "_"
        $text = preg_replace('/[^a-z0-9]+/', '_', $text);

        // // 4. Nettoie les éventuels "_" superflus qui se seraient créés au tout début ou à la fin de la chaîne
        $baseSlug = trim($text, '_');
        return $id . '_' . $baseSlug;

    }

    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setDesignation("Kit d'hygiène recyclable");
        $product->setPicture("p_kit_hygiene.png");
        $product->setPriceCurrent(24.99);
        $product->setShortDescr("Pour une salle de bain éco-friendly");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $product = new Product();
        $product->setDesignation("Shot tropical");
        $product->setPicture("p_shot_tropical.png");
        $product->setPriceCurrent(4.50);
        $product->setShortDescr("Fruits frais, pressés à froid");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $product = new Product();
        $product->setDesignation("Gourde en bois");
        $product->setPicture("p_gourde.png");
        $product->setPriceCurrent(16.90);
        $product->setShortDescr("50cl, bois d'olivier");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $product = new Product();
        $product->setDesignation("Disques Démaquillants x3");
        $product->setPicture("p_demaquillant.png");
        $product->setPriceCurrent(19.90);
        $product->setShortDescr("Solution efficace pour vous démaquiller en douceur");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $product = new Product();
        $product->setDesignation("Bougie Lavande & Patchouli");
        $product->setPicture("p_bougie.png");
        $product->setPriceCurrent(32.00);
        $product->setShortDescr("Cire naturelle");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $product = new Product();
        $product->setDesignation("Brosse à dent");
        $product->setPicture("p_brosse_a_dents.png");
        $product->setPriceCurrent(5.40);
        $product->setShortDescr("Bois de hêtre rouge issu de forêts gérées durablement");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $product = new Product();
        $product->setDesignation("Kit couvert en bois");
        $product->setPicture("p_kit_couvert_bois.png");
        $product->setPriceCurrent(5.40);
        $product->setShortDescr("Revêtement Bio en olivier & sac de transport");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $product = new Product();
        $product->setDesignation("Nécessaire, déodorant Bio");
        $product->setPicture("p_deodorant_bio.png");
        $product->setPriceCurrent(8.50);
        $product->setShortDescr("50ml déodorant à l’eucalyptus");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $product = new Product();
        $product->setDesignation("Savon Bio");
        $product->setPicture("p_savon_bio.png");
        $product->setPriceCurrent(18.90);
        $product->setShortDescr("Thé, Orange & Girofle");
        $product->setActive(1);
        $product->setCreatedAt($this->generateRandomDate());
        $product->setEan13($this->generateRandomEan13());
        $product->setLongDescr($this->generateLoremIpsum(100));
        $product->setSku($this->generateRandomSku());
        $product->setSlug("slug");
        $manager->persist($product);
        $manager->flush(); // Flush to get the ID
        $product->setSlug($this->generateSlug($product->getId(), $product->getDesignation()));

        $manager->flush();
    }
}
