<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Feature\Cortex;

use Axiom\Rivescript\Cortex\Node;

uses()
    ->beforeEach(function () {
        $this->rivescript = new \Axiom\Rivescript\Rivescript();;
    })
    ->group('feature_tests');


it('can detect comments', function () {
    $comment = "// This is a comment";
    $noComment = "+ This is a trigger";

    $node1 = new Node($comment, 0);
    $node2 = new Node($noComment, 0);

    $result1 = $node1->getCommand()->isComment();
    $result2 = $node2->getCommand()->isComment();

    assertTrue($result1);
    assertFalse($result2);
});


it('can detect empty lines', function () {
    $line1 = "";
    $line2 = " ";
    $line3 = "+ non empty line";

    $node1 = new Node($line1, 0);
    $node2 = new Node($line2, 0);
    $node3 = new Node($line3, 0);

    $result1 = $node1->getCommand()->isEmpty();
    $result2 = $node2->getCommand()->isEmpty();
    $result3 = $node3->getCommand()->isEmpty();

    assertTrue($result1);
    assertTrue($result2);
    assertFalse($result3);
});
