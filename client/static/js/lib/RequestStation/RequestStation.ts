/// <reference path='../Request/index.ts'/>
/// <reference path='../Modules/Converters/index.ts'/>
/// <reference path='../ResponseInterpreter/index.ts'/>
/// <reference path='../URLGenerator/index.ts'/>

class RequestStation
{
    public static RegisterUser(interpreter : IResponseInterpreter, data : FormData)
    {
        let generator = RequestStation.GetURLGenerator();
        let url       = generator.GetURL("users", "new");
        let request   = new AjaxRequest(interpreter, url, "post", true, data);
        request.Send();
    }

    public static RegisterPetrinet(interpreter : IResponseInterpreter, data : FormData)
    {
        let store = Store.GetInstance();
        let id = store.GetUserId();

        let generator = RequestStation.GetURLGenerator();
        let url       = generator.GetURL("petrinet", id.toString(), "new");
        let request   = new AjaxRequest(interpreter, url, "post", true, data);
        request.Send();
    }

    public static GetPetrinet(interpreter : IResponseInterpreter, id : number) {
        let generator = RequestStation.GetURLGenerator();
        let url       = generator.GetURL("petrinet", id.toString());
        let request   = new AjaxRequest(interpreter, url, "GET", true);
        request.Send();
    }

    public static GetPetrinetImage(interpreter : IResponseInterpreter, id : number) {
        let generator = RequestStation.GetURLGenerator();
        let url       = generator.GetURL("petrinet", id.toString(), "image");
        let request   = new AjaxRequest(interpreter, url, "GET", true);
        request.Send();
    }
    
    public static GetFeedback(interpreter : IResponseInterpreter, graph : Graph | string) {
        if(graph instanceof Graph) {
            graph = new GraphToJson(graph).Convert();
        }
        
        let uid = Store.GetInstance().GetUserId();
        let pid = Store.GetInstance().GetPetrinetId();
        let sid = Store.GetInstance().GetSessionId();

        let generator = RequestStation.GetURLGenerator();
        let url = generator.GetURL("petrinet", uid.toString(), pid.toString(), sid.toString(), "feedback");
        let request = new AjaxRequest(interpreter, url, "POST", true, graph, "application/json");
        request.Send();
    }

    public static SetSession(interpreter : IResponseInterpreter, uid: number, pid : number) {
        let generator = RequestStation.GetURLGenerator();
        let url = generator.GetURL("session", uid.toString(), pid.toString(), "new_session");
        let request = new AjaxRequest(interpreter, url, "POST", true);
        request.Send();
    }

    protected static GetURLGenerator()
    {
        let path = Settings.GetInstance().GetApiPath();
        let generator = new URLGenerator(path);
        return generator;
    }
}
