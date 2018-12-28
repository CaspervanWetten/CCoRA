/// <reference path='./lib/RequestStation/index.ts'/>
/// <reference path='./lib/Response/index.ts'/>
/// <reference path='./lib/Models/Settings.ts'/>
/// <reference path='./lib/Models/Store.ts'/>
/// <reference path='./lib/Utils/Extensions/Array.ts'/>
/// <reference path='./lib/Utils/Tools/SVG/Parser.ts'/>
/// <reference path='./lib/HTMLGenerators/index.ts'/>
/// <reference path='./lib/Modules/index.ts'/>
/// <reference path='./lib/Workspace/index.ts'/>
/// <reference path='./lib/Systems/TokenCount/index.ts'/>

class Main
{
    public static Main()
    {
        let apiPath = "";
        let settings = Settings.GetInstance();
        settings.SetApiPath(apiPath);
        
        let init = new Initializer();
        let store = Store.GetInstance();
        store.Attach(init);
        store.Init();

        // store.SetUserId(1);
        // store.SetPetrinetId(70);

        // let places = [
        //     "p1",
        //     "p2",
        //     "p3",
        //     "p4"
        // ];
        // let transitions = [
        //     "t1",
        //     "t2",
        //     "t3",
        //     "t4"
        // ];
        // let petrinet = new Petrinet(places, transitions);

        // store.SetPetrinet(petrinet);
    }
}

window.addEventListener("DOMContentLoaded", (e) => {
    Main.Main();
});

// window.onbeforeunload = function() {return true;};
