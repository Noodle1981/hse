<?php
$file = 'd:\ep-back\resources\js\Pages\Tecnico\MyAssignments.jsx';
$content = file_get_contents($file);

// Replace the status badge cell opening and closing with our dynamic checklist count
$target = '<span className={`px-3 py-1 rounded-lg text-[8px] font-black uppercase italic tracking-widest border transition-all duration-300';
$pos = strpos($content, $target);

if ($pos !== false) {
    echo "Found target!\n";
    // We want to replace the surrounding <td className="px-6 py-4"> to </td> block
    // Let's find the <td class... before this pos
    $td_start = strrpos(substr($content, 0, $pos), '<td className="px-6 py-4">');
    $td_end = strpos($content, '</td>', $pos) + 5;
    
    if ($td_start !== false && $td_end !== false) {
        $old_block = substr($content, $td_start, $td_end - $td_start);
        
        $new_block = '                                                 <td className="px-6 py-4">
                                                     {activeTab === \'inspections\' ? (
                                                         <span className="text-[11px] font-black text-white italic tracking-tight uppercase leading-none">
                                                             {item?.checklist_count || 0} {item?.checklist_count === 1 ? \'registro\' : \'registros\'}
                                                         </span>
                                                     ) : (
                                                         <span className={`px-3 py-1 rounded-lg text-[8px] font-black uppercase italic tracking-widest border transition-all duration-300
                                                             ${item?.status === \'completed\' || item?.status === \'finalizado\' ? \'text-emerald-500 bg-emerald-500/5 border-emerald-500/20\' : 
                                                               item?.status === \'in_progress\' ? \'text-ep-primary bg-ep-primary/5 border-ep-primary/20 animate-pulse\' : \'text-white/20 bg-white/5 border-white/5\'}
                                                         `}>
                                                             {item?.status === \'in_progress\' ? \'CURSO\' : (item?.status === \'completed\' || item?.status === \'finalizado\') ? \'FINAL\' : \'SOL\'}
                                                         </span>
                                                     )}
                                                 </td>';
                                                 
        $content = substr_replace($content, $new_block, $td_start, $td_end - $td_start);
        file_put_contents($file, $content);
        echo "Successfully updated MyAssignments.jsx!\n";
    } else {
        echo "Could not find surrounding td!\n";
    }
} else {
    echo "Target not found via strpos!\n";
}
