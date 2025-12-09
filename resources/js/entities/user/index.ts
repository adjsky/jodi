import Avatar from "./ui/Avatar.svelte";
import InfoActionRow from "./ui/InfoActionRow.svelte";
import InfoBlock from "./ui/InfoBlock.svelte";
import InfoSelectRow from "./ui/InfoSelectRow.svelte";
import InfoSettingRow from "./ui/InfoSettingRow.svelte";

export const User = {
    Avatar,
    Info: {
        Block: InfoBlock,
        SettingRow: InfoSettingRow,
        ActionRow: InfoActionRow,
        SelectRow: InfoSelectRow
    }
};
