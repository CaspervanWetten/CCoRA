/// <reference path='./index.ts'/>
/// <reference path='../Observer/index.ts'/>
/// <reference path='../Utils/Extensions/Array.ts'/>
/// <reference path='../Systems/State.ts'/>

interface SettingsObserver extends IObserver<Settings>
{
    Update(observable : Settings);
}

class Settings implements IObservable
{
    protected static Instance : Settings | undefined;

    // grid settings
    protected SnapGrid              : boolean;
    protected DisplayGrid           : boolean;
    protected HorizontalSteps       : number;
    protected VerticalSteps         : number;
    // drawing settings
    protected StatePadding          : number;
    protected StateHeight           : number;
    protected SeperationDistance    : number;
    protected EdgeRadius            : number;
    protected StateDisplayStyle     : StateDisplayStyle;
    // difficulty setting
    protected Difficulty            : ModelingDifficulty;
    // operational settings
    protected Debug                 : boolean;
    protected ApiPath               : string;

    protected Observers             : SettingsObserver[];

    protected constructor()
    {
        this.SetStandardSettings();
        this.Observers = [];
    }

    public static GetInstance()
    {
        if(this.Instance === undefined) {
            this.Instance = new Settings();
        }
        return this.Instance;
    }

    public SetStandardSettings()
    {
        this.SnapGrid           = true;
        this.DisplayGrid        = true;
        this.HorizontalSteps    = 50;
        this.VerticalSteps      = 50;

        this.StatePadding       = 30;
        this.StateHeight        = 40;
        this.SeperationDistance = 80;
        this.EdgeRadius         = 20;

        this.Debug              = false;
        
        this.StateDisplayStyle  = StateDisplayStyle.FULL;
        this.Difficulty         = ModelingDifficulty.NOVICE;
        
        this.ApiPath            = "";
    }

    //#region Observer functionality
    public Attach(observer : SettingsObserver)
    {
        this.Observers.push(observer);
        observer.Update(this);
    }

    public Detach(observer : SettingsObserver)
    {
        this.Observers.removeAt(this.Observers.indexOf(observer));
    }

    public Notify()
    {
        for(let i = 0; i < this.Observers.length; i++)
        {
            this.Observers[i].Update(this);
        }
    }
    //#endregion

    //#region Getters and Setters
    public GetSnapGrid()
    {
        return this.SnapGrid;
    }

    public SetSnapGrid(b : boolean)
    {
        this.SnapGrid = b;
        this.Notify();
    }

    public GetDisplayGrid()
    {
        return this.DisplayGrid;
    }

    public SetDisplayGrid(b : boolean)
    {
        this.DisplayGrid = b;
        this.Notify();
    }

    public GetHorizontalSteps()
    {
        return this.HorizontalSteps;
    }

    public SetHorizontalSteps(steps : number)
    {
        this.HorizontalSteps = steps;
        this.Notify();
    }

    public GetVerticalSteps()
    {
        return this.VerticalSteps;
    }

    public SetVerticalSteps(steps : number)
    {
        this.VerticalSteps = steps;
        this.Notify();
    }

    public GetEdgeRadius()
    {
        return this.EdgeRadius;
    }

    public GetStateHeight()
    {
        return this.StateHeight;
    }

    public GetStatePadding()
    {
        return this.StatePadding;
    }
    
    public GetSeperationDistance()
    {
        return this.SeperationDistance;
    }

    public GetStateDisplayStyle()
    {
        return this.StateDisplayStyle;
    }
    
    public SetStateDisplayStyle(style : StateDisplayStyle)
    {
        this.StateDisplayStyle = style;
        this.Notify();
    }

    public GetDifficulty()
    {
        return this.Difficulty;
    }

    public SetDifficulty(d : ModelingDifficulty) {
        this.Difficulty = d;
        this.Notify();
    }
    
    public GetDebug()
    {
        return this.Debug;
    }

    public SetDebug(tf:boolean)
    {
        this.Debug = tf;
        this.Notify();
    }

    public ToggleDebug()
    {
        this.Debug = !this.Debug;
        this.Notify();
    }
    
    public SetApiPath(path : string)
    {
        this.ApiPath = path;
    }

    public GetApiPath()
    {
        return this.ApiPath;
    }
    //#endregion
}