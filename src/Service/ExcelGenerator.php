<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExcelGenerator
{
    public function generateExcelFromProduits(array $produits, string $filename): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Référence');
        $sheet->setCellValue('C1', 'Nom');
        $sheet->setCellValue('D1', 'Prix (DT)');
        $sheet->setCellValue('E1', 'Stock');
        $sheet->setCellValue('F1', 'Description');

        // Données
        $row = 2;
        foreach ($produits as $produit) {
            $sheet->setCellValue('A'.$row, $produit->getIdProduit());
            $sheet->setCellValue('B'.$row, $produit->getRefProduit());
            $sheet->setCellValue('C'.$row, $produit->getNomProduit());
            $sheet->setCellValue('D'.$row, $produit->getPrixProduit());
            $sheet->setCellValue('E'.$row, $produit->getQuantiteProduit());
            $sheet->setCellValue('F'.$row, $produit->getDescriptionProduit());
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Créer la réponse
        $writer = new Xlsx($spreadsheet);
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        ));

        ob_start();
        $writer->save('php://output');
        $response->setContent(ob_get_clean());

        return $response;
    }
}