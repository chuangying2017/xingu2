<?php
namespace app\console;

use think\console\command\Command;
use think\console\Input;
use think\console\Output;
use think\console\output\formatter\Style;

class Colors extends Command
{
    protected function configure()
    {

          $this->setName('color')->setDescription('Show Color text');

    }

    protected function execute(Input $input, Output $output)
    {
        // 输出info样式
        $output->writeln("<info>this is info</info>");
        // 输出error样式
        $output->writeln("<error>this is error</error>");
        // 输出comment样式
        $output->writeln("<comment>this is comment</comment>");
        // 输出question样式
        $output->writeln("<question>this is question</question>");
        // 输出highlight样式
        $output->writeln("<highlight>this is highlight</highlight>");
        // 输出warning样式
        $output->writeln("<warning>this is warning</warning>");
        // 输出混合样式
        $output->writeln("this is <info>info</info>, this is <error>error</error>,this is <comment>comment</comment>,this is <question>question</question>,this is <highlight>highlight</highlight>, this is <warning>warning</warning>");
        // 自定义输出样式
        $output->getFormatter()->setStyle('custom', new Style('black', 'white'));
        $output->writeln("<custom>this is style</custom>");
    }
}