<?php
/**
 * This file is part of FacturaScripts
 * Copyright (C) 2017-2018 Carlos Garcia Gomez <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Core\Controller;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Lib\ExtendedController;

/**
 * Controller to edit a single item from the EditProducto model
 *
 * @author Carlos García Gómez          <carlos@facturascripts.com>
 * @author Artex Trading sa             <jcuello@artextrading.com>
 * @author Fco. Antonio Moreno Pérez    <famphuelva@gmail.com>
 */
class EditProducto extends ExtendedController\EditController
{

    /**
     * 
     * @return string
     */
    public function getModelClassName()
    {
        return 'Producto';
    }

    /**
     * Returns basic page attributes
     *
     * @return array
     */
    public function getPageData()
    {
        $pagedata = parent::getPageData();
        $pagedata['title'] = 'product';
        $pagedata['icon'] = 'fas fa-cube';
        $pagedata['menu'] = 'warehouse';
        $pagedata['showonmenu'] = false;

        return $pagedata;
    }

    /**
     * Load views
     */
    protected function createViews()
    {
        parent::createViews();
        $this->addEditListView('EditVariante', 'Variante', 'variants', 'fas fa-code-branch');
        $this->addEditListView('EditStock', 'Stock', 'stock', 'fas fa-tasks');
    }

    /**
     * 
     */
    protected function loadCustomStockWidget()
    {
        $references = [];
        $idproducto = $this->getViewModelValue('EditProducto', 'idproducto');
        $where = [new DataBaseWhere('idproducto', $idproducto)];
        foreach ($this->codeModel->all('variantes', 'referencia', 'referencia', false, $where) as $code) {
            $references[] = ['value' => $code->code, 'title' => $code->description];
        }

        $columnReference = $this->views['EditStock']->columnForName('reference');
        $columnReference->widget->setValuesFromArray($references, false);
    }

    /**
     * Load view data procedure
     *
     * @param string                      $viewName
     * @param ExtendedController\BaseView $view
     */
    protected function loadData($viewName, $view)
    {
        switch ($viewName) {
            case 'EditProducto':
                parent::loadData($viewName, $view);
                if ($view->model->nostock) {
                    $this->setSettings('EditStock', 'active', false);
                } else {
                    $this->loadCustomStockWidget();
                }
                break;

            case 'EditVariante':
            case 'EditStock':
                $idproducto = $this->getViewModelValue('EditProducto', 'idproducto');
                $where = [new DataBaseWhere('idproducto', $idproducto)];
                $view->loadData('', $where, ['referencia' => 'ASC']);
                break;
        }
    }
}
