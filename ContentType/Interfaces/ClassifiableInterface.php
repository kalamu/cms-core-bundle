<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\ContentType\Interfaces;

use Doctrine\Common\Collections\Collection;
use Kalamu\CmsAdminBundle\Entity\Term;

interface ClassifiableInterface
{
    /**
     * Add term
     *
     * @param \Kalamu\CmsCoreBundle\Entity\Term $term
     */
    public function addTerm(Term $term);

    /**
     * Remove term
     *
     * @param \Kalamu\CmsCoreBundle\Entity\Term $term
     */
    public function removeTerm(Term $term);

    /**
     * Get terms
     *
     * @return Collection
     */
    public function getTerms();
}