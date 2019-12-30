<?php

namespace Xthiago\PDFVersionConverter\Converter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This file is part of the PDF Version Converter.
 * (c) Thiago Rodrigues <xthiago@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Thiago Rodrigues <xthiago@gmail.com>
 */
class GhostscriptConverterTest extends TestCase
{
    protected $tmp;

    protected function setUp(): void
    {
        $this->tmp = __DIR__ . '/../files/stage/';

        if (!file_exists($this->tmp)) {
            mkdir($this->tmp);
        }
    }

    /**
     * @param string $file
     * @param string $newVersion
     * @dataProvider filesProvider
     */
    public function testMustConvertPDFVersionWithSuccess($file, $newVersion)
    {
        /** @var ObjectProphecy|Filesystem $fs */
        $fs = $this->prophesize('Symfony\Component\Filesystem\Filesystem');
        $fs->exists(Argument::type('string'))
            ->willReturn(true)
            ->shouldBeCalled();
        $fs->copy(
            Argument::type('string'),
            $file,
            true
        )
            ->willReturn(true)
            ->shouldBeCalled();

        /** @var ObjectProphecy|GhostscriptConverterCommand $command */
        $command = $this->prophesize('Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand');
        $command->run(
            $file,
            Argument::type('string'),
            $newVersion
        )
            ->willReturn(null)
            ->shouldBeCalled();

        $converter = new GhostscriptConverter(
            $command->reveal(),
            $fs->reveal(),
            $this->tmp
        );

        $converter->convert($file, $newVersion);
    }

    /**
     * @return array
     */
    public static function filesProvider()
    {
        return array(
            // file, new version
            array(__DIR__ . '/../files/stage/v1.1.pdf', '1.4'),
            array(__DIR__ . '/../files/stage/v1.2.pdf', '1.4'),
            array(__DIR__ . '/../files/stage/v1.3.pdf', '1.4'),
            array(__DIR__ . '/../files/stage/v1.4.pdf', '1.4'),
            array(__DIR__ . '/../files/stage/v1.5.pdf', '1.4'),
            array(__DIR__ . '/../files/stage/v1.6.pdf', '1.4'),
            array(__DIR__ . '/../files/stage/v1.7 filename with "Sp3ci4l"; <\'Ch4r5\'> !£$%&()=?^[]{}è@#§.pdf', '1.4'),
            array(__DIR__ . '/../files/stage/v1.7.pdf', '1.4'),
            array(__DIR__ . '/../files/stage/v2.0.pdf', '1.4'),
        );
    }
}