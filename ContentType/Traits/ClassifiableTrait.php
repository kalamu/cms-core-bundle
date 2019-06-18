<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\ContentType\Traits;

use Kalamu\CmsAdminBundle\Entity\Term;

trait ClassifiableTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $terms;

    /**
     * Add term
     *
     * @param \Kalamu\CmsCoreBundle\Entity\Term $term
     *
     * @return Actualite
     */
    public function addTerm(Term $term)
    {
        $this->terms[] = $term;

        return $this;
    }

    /**
     * Remove term
     *
     * @param \Kalamu\CmsCoreBundle\Entity\Term $term
     */
    public function removeTerm(Term $term)
    {
        $this->terms->removeElement($term);
    }

    /**
     * Get terms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Retourne tous les terms faisant parti de la taxonomie demandée
     * @param type $taxonomy
     * @return type
     */
    public function getTermsOfTaxonomy($taxonomy){
        return $this->terms->filter(function($term) use ($taxonomy){
            if(is_string($taxonomy)){
                return $taxonomy == $term->getTaxonomy()->getSlug() ? $term : null;
            }elseif($term->getTaxonomy() == $taxonomy){
                return $term;
            }
        });
    }

    /**
     * Retourne la liste des taxonomies pour lesquelles, l'entitée à au moins 1 term
     * @return array
     */
    public function getUsedTaxonomies(){
        $taxonomies = array();
        foreach($this->terms as $term){
            $taxSlug = $term->getTaxonomy()->getSlug();
            if(!isset($taxonomies[$taxSlug])){
                $taxonomies[$taxSlug] = $term->getTaxonomy();
            }
        }
        return $taxonomies;
    }
}