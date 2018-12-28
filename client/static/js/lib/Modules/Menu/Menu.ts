/// <reference path='../../HTMLGenerators/MenuBuilder/index.ts'/>
/// <reference path='../../Models/Settings.ts'/>

class Menu extends MenuBuilder
{
    public constructor()
    {
        super();
        let settings = Settings.GetInstance();
        let gridSettingsCat = "Grid Options";

        let snapToGrid = new Switcher(
            () => {settings.SetSnapGrid(true);},
            () => {settings.SetSnapGrid(false);},
            Number(settings.GetSnapGrid()) as SwitchState
        );

        let displayGrid = new Switcher(
            () => {settings.SetDisplayGrid(true);},
            () => {settings.SetDisplayGrid(false);},
            Number(settings.GetDisplayGrid()) as SwitchState
        );

        this.AddMenuItem(gridSettingsCat, "SnapGrid", new MenuItem<SwitchState>("Snap to Grid", snapToGrid));
        this.AddMenuItem(gridSettingsCat, "DisplayGrid", new MenuItem<SwitchState>("Display Grid", displayGrid));

        let displaySettingsCat = "Graph Options";
        let stateStyle = new Switcher(
            () => {settings.SetStateDisplayStyle(StateDisplayStyle.FULL);},
            () => {settings.SetStateDisplayStyle(StateDisplayStyle.NON_NEGATIVE);},
            Number(settings.GetStateDisplayStyle()) as SwitchState
        );
        stateStyle.OffLabel = "Minimal";
        stateStyle.OnLabel = "Full";

        this.AddMenuItem(displaySettingsCat, "StateDisplayStyle", new MenuItem<SwitchState>("State Display Style", stateStyle));

        let feedbackSettingsCat = "Feedback Options";
        let difficultySetting = new Switcher(
            () => {settings.SetDifficulty(ModelingDifficulty.ADVANCED);},
            () => {settings.SetDifficulty(ModelingDifficulty.NOVICE);},
            Number(settings.GetDifficulty()) as SwitchState
        );
        difficultySetting.OffLabel = "Immediate";
        difficultySetting.OnLabel = "On Request";

        this.AddMenuItem(feedbackSettingsCat, "Difficulty", new MenuItem<SwitchState>("Feedback Style", difficultySetting));

        settings.Attach(this);
    }
}