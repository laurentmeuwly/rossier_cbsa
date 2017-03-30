<?php

namespace InventoryBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InventoryBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // Liste des noms de catégorie à ajouter
    $names = array(
      'Tuyau',
      'Ciment',
      'Brique',
      'Outil',
    );

    foreach ($names as $name) {
      $category = new Category();
      $category->setName($name);
      $category->setDisplayOrder(1);

      $manager->persist($category);
    }

    $manager->flush();
  }
}