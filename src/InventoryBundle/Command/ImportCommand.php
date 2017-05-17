<?php

namespace InventoryBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use InventoryBundle\Entity\Category;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\Unit;
 
class ImportCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('import:csv')
        ->setDescription('Import categories from CSV file')
        ->addArgument('entity', InputArgument::REQUIRED, 'An entity name');
    }
    
   protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Showing when the script is launched
        $now = new \DateTime();
        $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
        
        // Importing CSV on DB via Doctrine ORM
        $entity = $input->getArgument('entity');
        switch ($entity) {
        	case 'category':
        		$this->importCategories($input, $output);
        		break;
        	case 'product':
        		$this->importProducts($input, $output);
        		break;
        	case 'site':
        		$this->importSites($input, $output);
        		break;
        	default:
        		$output->writeln('Nothing to import');
        }
        
        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
    }
    
    protected function importCategories(InputInterface $input, OutputInterface $output)
    {
        // Getting php array of data from CSV
        $data = $this->get('categories.csv', $input, $output);
        
        // Getting doctrine manager
        $em = $this->getContainer()->get('doctrine')->getManager();
        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        // Define the size of record, the frequency for persisting the data and the current index of records
        $size = count($data);
        $batchSize = 20;
        $i = 1;
        
        // Starting progress
        $progress = new ProgressBar($output, $size);
        $progress->start();

        // Processing on each row of data
        foreach($data as $row) {

            $category = $em->getRepository('InventoryBundle:Category')
                       ->findOneByName($row['name']);
						 
			// If the category does not exist we create one
            if(!is_object($category)){
                $category = new Category();
                $category->setName($row['name']);
            }
            
			// Updating info
            if($row['display_order']=='') $row['display_order']=1;
            	$category->setDisplayOrder($row['display_order']);

            $category->setImage($row['image']);
            
            
            if($row['parent']!='') {
            	$parent = $em->getRepository('InventoryBundle:Category')
            			->findOneByName($row['parent']);
            	$category->setParent($parent);
            }
			
			// Do stuff here !
	
			// Persisting the current category
            $em->persist($category);
            
			// Each 20 categories persisted we flush everything
            if (($i % $batchSize) === 0) {
 
                $em->flush();
				// Detaches all objects from Doctrine for memory save
                $em->clear();
                
				// Advancing for progress display on console
                $progress->advance($batchSize);
				
                $now = new \DateTime();
                $output->writeln(' of categories imported ... | ' . $now->format('d-m-Y G:i:s'));
 
            }
 
            $i++;
 
        }
		
		// Flushing and clear data on queue
        $em->flush();
        $em->clear();
		
		// Ending the progress bar process
        $progress->finish();
    }
    
    protected function importProducts(InputInterface $input, OutputInterface $output)
    {
    	// Getting php array of data from CSV
    	$data = $this->get('products.csv', $input, $output);
    	
    	// Getting doctrine manager
    	$em = $this->getContainer()->get('doctrine')->getManager();
    	// Turning off doctrine default logs queries for saving memory
    	$em->getConnection()->getConfiguration()->setSQLLogger(null);
    	
    	// Define the size of record, the frequency for persisting the data and the current index of records
    	$size = count($data);
    	$batchSize = 20;
    	$i = 1;
    	
    	// Starting progress
    	$progress = new ProgressBar($output, $size);
    	$progress->start();
    	
    	// Processing on each row of data
    	foreach($data as $row) {
    	
    		$product = $em->getRepository('InventoryBundle:Product')
    		->findOneByName($row['name']);
    			
    		// If the product does not exist we create one
    		if(!is_object($product)){
    			sleep(1);
    			$product = new Product();
    			$product->setName($row['name']);
    		}
    	
    		// Updating info
    		$product->setDisplayOrder(1);
    		$product->setStock(0);
    		$product->setIsActive($row['active']);
    		$product->setImage($row['image']);
    		$product->setComment($row['comment']);
    		$product->setCostPrice($row['cost_price']);
    		$product->setToBePrinted($row['to_display']);
    		$product->setIsManualAllowed($row['manual_allowed']);
    	
    		if($row['unit']!='') {
    			$unit = $em->getRepository('InventoryBundle:Unit')
    					->findOneByName($row['unit']);
    			$product->setUnit($unit);	
    		}
    	
    		if($row['category']!='') {
    			$category = $em->getRepository('InventoryBundle:Category')
    			->findOneByName($row['category']);
    			$product->setCategory($category);
    		}
    			
    		// Do stuff here !
    	
    		// Persisting the current product
    		$em->persist($product);
    	
    		// Each 20 products persisted we flush everything
    		if (($i % $batchSize) === 0) {
    	
    			$em->flush();
    			// Detaches all objects from Doctrine for memory save
    			$em->clear();
    	
    			// Advancing for progress display on console
    			$progress->advance($batchSize);
    	
    			$now = new \DateTime();
    			$output->writeln(' of products imported ... | ' . $now->format('d-m-Y G:i:s'));
    	
    		}
    	
    		$i++;
    	
    	}
    	
    	// Flushing and clear data on queue
    	$em->flush();
    	$em->clear();
    	
    	// Ending the progress bar process
    	$progress->finish();
    }
    
    protected function importSites(InputInterface $input, OutputInterface $output)
    {
    	// Getting php array of data from CSV
    	$data = $this->get('sites.csv', $input, $output);
    	
    	// Getting doctrine manager
    	$em = $this->getContainer()->get('doctrine')->getManager();
    	// Turning off doctrine default logs queries for saving memory
    	$em->getConnection()->getConfiguration()->setSQLLogger(null);
    	
    	// Define the size of record, the frequency for persisting the data and the current index of records
    	$size = count($data);
    	$batchSize = 20;
    	$i = 1;
    	
    	// Starting progress
    	$progress = new ProgressBar($output, $size);
    	$progress->start();
    }
 
    protected function get(String $file, InputInterface $input, OutputInterface $output) 
    {
        // Getting the CSV from filesystem
        $fileName = 'docs/'. $file;
        
        // Using service for converting CSV to PHP Array
        $converter = $this->getContainer()->get('import.csvtoarray');
        $data = $converter->convert($fileName, ',');
        
        return $data;
    }
    
}