<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Attributes\FindResponse;
use Axiom\Rivescript\Cortex\Attributes\ResponseDetector;
use Axiom\Rivescript\Cortex\Commands\Response\AtomicResponse;
use Axiom\Rivescript\Cortex\Commands\Response\Detectors\ArrayDetector;
use Axiom\Rivescript\Cortex\Commands\Response\Detectors\WeightedDetector;

/**
 * ContinueCommand class
 *
 * Description:
 *
 * This handle and validate the command type "continue".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#CONTINUE
 *
 * Note: This class is named ContinueCommand because Continue is
 *       a reserved word in PHP.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class ContinueCmd extends ResponseAbstract implements ResponseInterface
{

    /**
     * At this point in time continue does not have
     * a syntax check because its continues a
     * response and works like glue.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {
        return $this->isSyntaxValid();
    }

}
