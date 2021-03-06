<?php

namespace App\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Twig\AppExtension;

class CategoryTest extends KernelTestCase
{
    protected $mockedCategoryTreeFrontPage;
    protected $mockedCategoryTreeAdminList;
    protected $mockedCategoryTreeAdminOptionList;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $urlgenerator = $kernel->getContainer()->get('router');
        $tested_classes = [
            'CategoryTreeAdminList',
            'CategoryTreeAdminOptionList',
            'CategoryTreeFrontPage'
        ];
        foreach($tested_classes as $class)
        {
            $name = 'mocked'.$class;
            $this->$name = $this->getMockBuilder('App\Utils\\'.$class)
                ->disableOriginalConstructor()
                ->setMethods() // if no, all methods return null unless mocked
                ->getMock();
            $this->$name->urlgenerator = $urlgenerator;
        }

    }


    /**
     * @dataProvider dataForCategoryTreeFrontPage
     * @param $string
     * @param $array
     * @param $id
     */
    public function testCategoryTreeFrontPage($string, $array, $id)
    {
        $this->mockedCategoryTreeFrontPage->categoriesArrayFromDb = $array;
        $this->mockedCategoryTreeFrontPage->slugger = new AppExtension;
        $main_parent_id = $this->mockedCategoryTreeFrontPage->getMainParent($id)['id'];
        $array = $this->mockedCategoryTreeFrontPage->buildTree($main_parent_id);
        $this->assertSame($string, $this->mockedCategoryTreeFrontPage->getCategoryList($array));
    }

    /**
     * @dataProvider dataForCategoryTreeAdminOptionList
     * @param $arrayToCompare
     * @param $arrayFromDb
     */
    public function testCategoryTreeAdminOptionList($arrayToCompare, $arrayFromDb)
    {
        $this->mockedCategoryTreeAdminOptionList->categoriesArrayFromDb = $arrayFromDb;
        $arrayFromDb = $this->mockedCategoryTreeAdminOptionList->buildTree();
        $this->assertSame($arrayToCompare, $this->mockedCategoryTreeAdminOptionList->getCategoryList($arrayFromDb));
    }

    public function dataForCategoryTreeFrontPage()
    {
        yield [
            '<ul><li><a href="/videolist/childrens-books/15">childrens-books</a></li><li><a href="/videolist/kindle-ebooks/16">kindle-ebooks</a></li></ul>',
            [
                ['name'=>'Books','id'=>3, 'parent_id'=>null],
                ['name'=>'Children\'s Books','id'=>15, 'parent_id'=>3],
                ['name'=>'Kindle eBooks','id'=>16, 'parent_id'=>3],
            ],
            3
        ];

//        yield [
//            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
//            [
//                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
//                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
//                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
//                ['name'=>'HP','id'=>14, 'parent_id'=>8]
//            ],
//            6
//        ];
//
//        yield [
//            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
//            [
//                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
//                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
//                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
//                ['name'=>'HP','id'=>14, 'parent_id'=>8]
//            ],
//            8
//        ];
//
//        yield [
//            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
//            [
//                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
//                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
//                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
//                ['name'=>'HP','id'=>14, 'parent_id'=>8]
//            ],
//            14
//
//        ];
    }

    public function dataForCategoryTreeAdminOptionList()
    {
        yield [
            [
                ['name'=>'Electronics','id'=>1],
                ['name'=>'--Computers','id'=>6],
                ['name'=>'----Laptops','id'=>8],
                ['name'=>'------Apple','id'=>10]
            ],
            [
                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
                ['name'=>'Apple','id'=>10, 'parent_id'=>8]
            ]
        ];
    }
}
