<?php

namespace Tests\Unit;

use App\Support\WordPressContent;
use PHPUnit\Framework\TestCase;

class WordPressContentTest extends TestCase
{
    public function test_it_removes_divi_containers_and_keeps_the_text(): void
    {
        $html = '[et_pb_section bb_built="1"][et_pb_row][et_pb_column type="4_4"][et_pb_text]'
            . '<div>El Intendente firmó el convenio.</div>'
            . '[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]';

        $this->assertSame('<div>El Intendente firmó el convenio.</div>', WordPressContent::quitarShortcodesDivi($html));
    }

    public function test_it_turns_image_shortcodes_into_img_tags(): void
    {
        $html = '[et_pb_section][et_pb_row][et_pb_column][et_pb_text]<p>Texto</p>[/et_pb_text]'
            . '[et_pb_image _builder_version="3.6" src="/images/2026/08/foto-1_resultado.webp" /]'
            . '[et_pb_image _builder_version="3.6" src="/images/2026/08/foto-2_resultado.webp" /]'
            . '[/et_pb_column][/et_pb_row][/et_pb_section]';

        $resultado = WordPressContent::quitarShortcodesDivi($html);

        $this->assertStringContainsString('<img src="/images/2026/08/foto-1_resultado.webp" alt="">', $resultado);
        $this->assertStringContainsString('<img src="/images/2026/08/foto-2_resultado.webp" alt="">', $resultado);
        $this->assertStringContainsString('<p>Texto</p>', $resultado);
        $this->assertStringNotContainsString('[et_pb', $resultado);
        $this->assertStringNotContainsString('[/et_pb', $resultado);
    }

    public function test_image_attributes_are_escaped_and_alt_is_kept(): void
    {
        $resultado = WordPressContent::quitarShortcodesDivi('[et_pb_image src="/a.webp" alt="Dos &quot;cuadras&quot; <nuevas>" /]');

        $this->assertStringContainsString('alt="Dos &quot;cuadras&quot; &lt;nuevas&gt;"', $resultado);
        $this->assertStringNotContainsString('<nuevas>', $resultado);
    }

    public function test_an_image_shortcode_without_src_is_dropped(): void
    {
        $this->assertSame('<p>ok</p>', WordPressContent::quitarShortcodesDivi('<p>ok</p>[et_pb_image _builder_version="3.6" /]'));
    }

    public function test_content_without_shortcodes_is_returned_untouched(): void
    {
        $html = "<p>Texto normal con [corchetes] y  espacios</p>\n";

        $this->assertSame($html, WordPressContent::quitarShortcodesDivi($html));
    }

    public function test_square_brackets_that_are_not_divi_are_kept(): void
    {
        $html = '[et_pb_text]<p>Ver [nota 1] y [ordenanza 4521]</p>[/et_pb_text]';

        $this->assertSame('<p>Ver [nota 1] y [ordenanza 4521]</p>', WordPressContent::quitarShortcodesDivi($html));
    }
}
