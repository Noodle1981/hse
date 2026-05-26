<?php
$file = 'd:\ep-back\resources\js\Pages\Tecnico\MyAssignments.jsx';
$content = file_get_contents($file);

$target = "                                                 <td className=\"px-6 py-4\">\r\n                                                     {activeTab === 'inspections' ? (\r\n                                                         <span className=\"text-[11px] font-black text-white italic tracking-tight uppercase leading-none\">\r\n                                                             {item?.checklist_count || 0} {item?.checklist_count === 1 ? 'registro' : 'registros'}\r\n                                                         </span>\r\n                                                     ) : (";

// Fallback to match regardless of CRLF or LF:
if (strpos($content, $target) === false) {
    // Attempt with LF line endings
    $target = "<td className=\"px-6 py-4\">\n                                                     {activeTab === 'inspections' ? (\n                                                         <span className=\"text-[11px] font-black text-white italic tracking-tight uppercase leading-none\">\n                                                             {item?.checklist_count || 0} {item?.checklist_count === 1 ? 'registro' : 'registros'}\n                                                         </span>\n                                                     ) : (";
}

$pos = strpos($content, $target);

if ($pos !== false) {
    echo "Found cell target!\n";
    
    $new_block = "<td className=\"px-6 py-4\">
                                                     {activeTab === 'inspections' ? (
                                                         <div className=\"flex flex-col gap-1.5\">
                                                             <div className=\"flex items-center gap-2\">
                                                                 <span className=\"text-[11px] font-black text-white italic tracking-tight uppercase leading-none\">
                                                                     {item?.checklist_count || 0} {item?.checklist_count === 1 ? 'registro' : 'registros'}
                                                                 </span>
                                                                 {item?.completed_count > 0 && (
                                                                     <>
                                                                         <span className=\"text-[9px] font-bold text-white/20 uppercase tracking-widest leading-none\">|</span>
                                                                         <span className={`text-[10px] font-black uppercase italic tracking-tight leading-none flex items-center gap-1
                                                                             \${item?.passed_count === item?.completed_count ? 'text-emerald-400' : 'text-red-500'}
                                                                         `}>
                                                                             Exitosos: {item?.passed_count}/{item?.completed_count}
                                                                             {item?.passed_count !== item?.completed_count && <span>⚠️</span>}
                                                                         </span>
                                                                     </>
                                                                 )}
                                                             </div>
                                                             {item?.compliance_pct !== null && (
                                                                 <div className=\"flex items-center gap-2\">
                                                                     <span className=\"text-[8px] font-bold text-white/30 uppercase tracking-[0.2em] leading-none italic\">
                                                                         Cumplimiento:
                                                                     </span>
                                                                     <span className={`text-[10px] font-black leading-none
                                                                         \${item?.compliance_pct === 100 ? 'text-emerald-400 glow-text-emerald' : 'text-red-500 glow-text-red'}
                                                                     `}>
                                                                         {item?.compliance_pct}%
                                                                     </span>
                                                                 </div>
                                                             )}
                                                         </div>
                                                     ) : (";
                                                     
    $content = substr_replace($content, $new_block, $pos, strlen($target));
    file_put_contents($file, $content);
    echo "Successfully updated MyAssignments.jsx with KPI metrics!\n";
} else {
    echo "Cell target not found!\n";
}
