var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
/// <reference path='./index.ts'/>
var AjaxRequest = /** @class */ (function () {
    function AjaxRequest(caller, url, method, async, data, contentType) {
        if (method === void 0) { method = "GET"; }
        if (async === void 0) { async = true; }
        if (data === void 0) { data = undefined; }
        if (contentType === void 0) { contentType = ""; }
        this.Caller = caller;
        this.Url = url;
        this.Method = method.toUpperCase().trim();
        this.Async = async;
        this.Data = data;
        this.ContentType = contentType;
    }
    AjaxRequest.prototype.Send = function () {
        var _this = this;
        var request = new XMLHttpRequest();
        request.open(this.Method, this.Url, this.Async);
        if (this.ContentType.length > 0) {
            request.setRequestHeader("Content-Type", "application/json");
        }
        request.onreadystatechange = function (e) {
            if (request.readyState === request.DONE) {
                if (request.status >= 200 && request.status < 300) {
                    _this.Caller.ReceiveSuccess(request.status, request.responseText);
                }
                else {
                    _this.Caller.ReceiveFailure(request.status, request.responseText);
                }
            }
        };
        if (this.Data) {
            request.send(this.Data);
        }
        else {
            request.send();
        }
        this.Caller.ReceiveBusy();
    };
    return AjaxRequest;
}());
///<reference path='./Request.ts'/>
/// <reference path='./index.ts'/>
var SystemElement = /** @class */ (function () {
    function SystemElement(id) {
        this.Id = id;
    }
    SystemElement.prototype.GetId = function () {
        return this.Id;
    };
    return SystemElement;
}());
/// <reference path='./index.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='./IObservable.ts'/>
/// <reference path='./IObserver.ts'/>
Array.prototype.removeAt = function (index) {
    this.swap(index, this.length - 1);
    this.pop();
};
Array.prototype.swap = function (i, j) {
    var temp = this[i];
    this[i] = this[j];
    this[j] = temp;
};
/// <reference path='../Observer/index.ts'/>
/// <reference path='../Systems/index.ts'/>
/// <reference path='../Utils/Extensions/Array.ts'/>
var Store = /** @class */ (function () {
    function Store() {
        this.Observers = [];
    }
    Store.prototype.Init = function () {
        this.UserId = undefined;
        this.PetrinetId = undefined;
        this.SessionId = undefined;
        this.Graph = new Graph();
        this.Notify();
    };
    Store.GetInstance = function () {
        if (!this.Instance) {
            this.Instance = new Store();
        }
        return this.Instance;
    };
    Store.prototype.Attach = function (observer) {
        this.Observers.push(observer);
    };
    Store.prototype.Detach = function (observer) {
        this.Observers.removeAt(this.Observers.indexOf(observer));
    };
    Store.prototype.Notify = function () {
        for (var i = 0; i < this.Observers.length; i++) {
            this.Observers[i].Update(this);
        }
    };
    Store.prototype.GetGraph = function () {
        return this.Graph;
    };
    Store.prototype.GetUserId = function () {
        return this.UserId;
    };
    Store.prototype.SetUserId = function (id) {
        this.UserId = id;
        this.Notify();
    };
    Store.prototype.GetPetrinetId = function () {
        return this.PetrinetId;
    };
    Store.prototype.SetPetrinetId = function (id) {
        this.PetrinetId = id;
        this.Notify();
    };
    Store.prototype.GetSessionId = function () {
        return this.SessionId;
    };
    Store.prototype.SetSessionId = function (id) {
        this.SessionId = id;
        this.Notify();
    };
    Store.prototype.GetPetrinet = function () {
        return this.Petrinet;
    };
    Store.prototype.SetPetrinet = function (p) {
        this.Petrinet = p;
        this.Notify();
    };
    return Store;
}());
var ModelingDifficulty;
(function (ModelingDifficulty) {
    ModelingDifficulty[ModelingDifficulty["NOVICE"] = 0] = "NOVICE";
    ModelingDifficulty[ModelingDifficulty["ADVANCED"] = 1] = "ADVANCED";
})(ModelingDifficulty || (ModelingDifficulty = {}));
var StateDisplayStyle;
(function (StateDisplayStyle) {
    StateDisplayStyle[StateDisplayStyle["NON_NEGATIVE"] = 0] = "NON_NEGATIVE";
    StateDisplayStyle[StateDisplayStyle["FULL"] = 1] = "FULL";
})(StateDisplayStyle || (StateDisplayStyle = {}));
/// <reference path='./ModelingDifficulty.ts'/>
/// <reference path='./StateDisplayStyle.ts'/>
/// <reference path='./Settings.ts'/>
/// <reference path='./Store.ts'/>
/// <reference path='./enums/index.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../Observer/index.ts'/>
/// <reference path='../Utils/Extensions/Array.ts'/>
/// <reference path='../Systems/State.ts'/>
var Settings = /** @class */ (function () {
    function Settings() {
        this.SetStandardSettings();
        this.Observers = [];
    }
    Settings.GetInstance = function () {
        if (this.Instance === undefined) {
            this.Instance = new Settings();
        }
        return this.Instance;
    };
    Settings.prototype.SetStandardSettings = function () {
        this.SnapGrid = true;
        this.DisplayGrid = true;
        this.HorizontalSteps = 50;
        this.VerticalSteps = 50;
        this.StatePadding = 30;
        this.StateHeight = 40;
        this.SeperationDistance = 80;
        this.EdgeRadius = 20;
        this.Debug = false;
        this.StateDisplayStyle = StateDisplayStyle.FULL;
        this.Difficulty = ModelingDifficulty.NOVICE;
        this.ApiPath = "";
    };
    //#region Observer functionality
    Settings.prototype.Attach = function (observer) {
        this.Observers.push(observer);
        observer.Update(this);
    };
    Settings.prototype.Detach = function (observer) {
        this.Observers.removeAt(this.Observers.indexOf(observer));
    };
    Settings.prototype.Notify = function () {
        for (var i = 0; i < this.Observers.length; i++) {
            this.Observers[i].Update(this);
        }
    };
    //#endregion
    //#region Getters and Setters
    Settings.prototype.GetSnapGrid = function () {
        return this.SnapGrid;
    };
    Settings.prototype.SetSnapGrid = function (b) {
        this.SnapGrid = b;
        this.Notify();
    };
    Settings.prototype.GetDisplayGrid = function () {
        return this.DisplayGrid;
    };
    Settings.prototype.SetDisplayGrid = function (b) {
        this.DisplayGrid = b;
        this.Notify();
    };
    Settings.prototype.GetHorizontalSteps = function () {
        return this.HorizontalSteps;
    };
    Settings.prototype.SetHorizontalSteps = function (steps) {
        this.HorizontalSteps = steps;
        this.Notify();
    };
    Settings.prototype.GetVerticalSteps = function () {
        return this.VerticalSteps;
    };
    Settings.prototype.SetVerticalSteps = function (steps) {
        this.VerticalSteps = steps;
        this.Notify();
    };
    Settings.prototype.GetEdgeRadius = function () {
        return this.EdgeRadius;
    };
    Settings.prototype.GetStateHeight = function () {
        return this.StateHeight;
    };
    Settings.prototype.GetStatePadding = function () {
        return this.StatePadding;
    };
    Settings.prototype.GetSeperationDistance = function () {
        return this.SeperationDistance;
    };
    Settings.prototype.GetStateDisplayStyle = function () {
        return this.StateDisplayStyle;
    };
    Settings.prototype.SetStateDisplayStyle = function (style) {
        this.StateDisplayStyle = style;
        this.Notify();
    };
    Settings.prototype.GetDifficulty = function () {
        return this.Difficulty;
    };
    Settings.prototype.SetDifficulty = function (d) {
        this.Difficulty = d;
        this.Notify();
    };
    Settings.prototype.GetDebug = function () {
        return this.Debug;
    };
    Settings.prototype.SetDebug = function (tf) {
        this.Debug = tf;
        this.Notify();
    };
    Settings.prototype.ToggleDebug = function () {
        this.Debug = !this.Debug;
        this.Notify();
    };
    Settings.prototype.SetApiPath = function (path) {
        this.ApiPath = path;
    };
    Settings.prototype.GetApiPath = function () {
        return this.ApiPath;
    };
    return Settings;
}());
/// <reference path='./index.ts'/>
/// <reference path='../Models/Settings.ts'/>
/// <reference path='../Models/Store.ts'/>
/// <reference path='../../vendor/Definitions/Hashtable.d.ts'/>
var State = /** @class */ (function (_super) {
    __extends(State, _super);
    function State(id) {
        var _this = _super.call(this, id) || this;
        _this.replaceDuplicateKey = true;
        _this.Map = new Hashtable();
        // assign zero to every place
        var petrinet = Store.GetInstance().GetPetrinet();
        if (petrinet != null) {
            var places = petrinet.GetPlaces();
            for (var i = 0; i < places.length; i++) {
                _this.Add(places[i], 0);
            }
        }
        return _this;
    }
    State.prototype.Add = function (place, tokens) {
        if (typeof tokens == "number")
            tokens = new IntToken(tokens);
        this.Map.put(place, tokens);
    };
    State.prototype.GetPlace = function (place) {
        if (!this.Map.containsKey(place))
            return undefined;
        return this.Map.get(place);
    };
    State.prototype.ToDisplayString = function () {
        var settings = Settings.GetInstance();
        var style = settings.GetStateDisplayStyle();
        var keys = this.Map.keys();
        keys.sort();
        var elems = [];
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            var val = this.Map.get(key);
            // skip zero's when enabled
            if (val instanceof IntToken && val.value == 0 && style == StateDisplayStyle.NON_NEGATIVE)
                continue;
            var s = key + ":" + this.Map.get(key).ToString();
            elems.push(s);
        }
        var res = elems.join(" ");
        return res;
    };
    State.prototype.ToSystemString = function () {
        var keys = this.Map.keys();
        keys.sort();
        var elems = [];
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            var val = this.Map.get(key);
            if (val instanceof IntToken && val.value == 0)
                continue;
            var s = key + ":" + this.Map.get(key).ToString();
            elems.push(s);
        }
        var res = elems.join(", ");
        return res;
    };
    State.prototype.SetId = function (id) {
        this.Id = id;
    };
    State.prototype.GetMap = function () {
        return this.Map;
    };
    State.prototype.equals = function (other) {
        return this.Id == other.Id;
    };
    State.prototype.hashCode = function () {
        return this.Id;
    };
    return State;
}(SystemElement));
var TokenCount = /** @class */ (function () {
    function TokenCount() {
    }
    return TokenCount;
}());
/// <reference path='./index.ts'/>
var IntToken = /** @class */ (function (_super) {
    __extends(IntToken, _super);
    function IntToken(val) {
        var _this = _super.call(this) || this;
        _this.value = val;
        return _this;
    }
    IntToken.prototype.Add = function (i) {
        if (typeof i == "number") {
            return new IntToken(i + this.value);
        }
        else if (i instanceof IntToken) {
            return new IntToken(this.value + i.value);
        }
        return new OmegaToken();
    };
    IntToken.prototype.Subtract = function (i) {
        if (typeof i == "number") {
            return new IntToken(this.value - i);
        }
        else if (i instanceof IntToken) {
            return new IntToken(this.value + i.value);
        }
        return new OmegaToken();
    };
    IntToken.prototype.ToString = function () {
        return this.value.toString();
    };
    return IntToken;
}(TokenCount));
/// <reference path='./index.ts'/>
var OmegaToken = /** @class */ (function (_super) {
    __extends(OmegaToken, _super);
    function OmegaToken() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    OmegaToken.prototype.Add = function (i) { return this; };
    OmegaToken.prototype.Subtract = function (i) { return this; };
    OmegaToken.prototype.ToString = function () { return "ω"; };
    return OmegaToken;
}(TokenCount));
/// <reference path='./TokenCount.ts'/>
/// <reference path='./IntToken.ts'/>
/// <reference path='./OmegaToken.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../State.ts'/>
/// <reference path='../../../vendor/Definitions/Hashset.d.ts'/>
var Petrinet = /** @class */ (function () {
    // protected InitialMarking    : State;
    function Petrinet(places, transitions) {
        this.Places = new HashSet();
        this.Transitions = new HashSet();
        for (var i = 0; i < places.length; i++) {
            this.Places.add(places[i]);
        }
        for (var i = 0; i < transitions.length; i++) {
            this.Transitions.add(transitions[i]);
        }
    }
    Petrinet.prototype.GetPlaces = function () {
        return this.Places.values();
    };
    Petrinet.prototype.GetTransitions = function () {
        return this.Transitions.values();
    };
    return Petrinet;
}());
/// <reference path='./index.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='./index.ts'/>
var Flow = /** @class */ (function () {
    function Flow() {
    }
    return Flow;
}());
/// <reference path='./Petrinet.ts'/>
/// <reference path='./Place.ts'/>
/// <reference path='./Transition.ts'/>
/// <reference path='./Flow.ts'/>
/// <reference path='./SystemElement.ts'/>
/// <reference path='./State.ts'/>
/// <reference path='./TokenCount/index.ts'/>
/// <reference path='./Graph/index.ts'/>
/// <reference path='./Petrinet/index.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>
/// <reference path='../../../vendor/Definitions/Hashtable.d.ts'/>
var Edge = /** @class */ (function (_super) {
    __extends(Edge, _super);
    function Edge(id, from, to, transition) {
        var _this = _super.call(this, id) || this;
        _this.replaceDuplicateKey = true;
        _this.From = from;
        _this.To = to;
        _this.Transition = transition;
        return _this;
    }
    Edge.prototype.ToString = function () {
        var s = "Edge from: " + this.From.ToDisplayString() + ", to: " + this.To.ToDisplayString();
        return s;
    };
    //#region Getters and Setters
    Edge.prototype.GetFromState = function () {
        return this.From;
    };
    Edge.prototype.GetToState = function () {
        return this.To;
    };
    Edge.prototype.GetTransition = function () {
        return this.Transition;
    };
    Edge.prototype.SetFromState = function (state) {
        this.From = state;
    };
    Edge.prototype.SetToState = function (state) {
        this.To = state;
    };
    Edge.prototype.SetTransition = function (trans) {
        this.Transition = trans;
    };
    //#endregion
    //#region hashtable functionality
    Edge.prototype.equals = function (other) {
        return this.ToString() === other.ToString();
    };
    Edge.prototype.hashCode = function () {
        return this.ToString();
    };
    return Edge;
}(SystemElement));
/// <reference path='./Edge.ts'/>
/// <reference path='../State.ts'/>
/// <reference path='../../../vendor/Definitions/Hashtable.d.ts'/>
/// <reference path='../../../vendor/Definitions/Hashset.d.ts'/>
var Graph = /** @class */ (function () {
    function Graph() {
        this.States = new Hashtable();
        this.Edges = new Hashtable();
        this.Initial = undefined;
    }
    Graph.prototype.AddState = function (state) {
        var id = state.GetId();
        this.States.put(id, state);
    };
    Graph.prototype.RemoveState = function (state) {
        if (this.States.containsKey(state.GetId())) {
            var edges = this.Edges.values();
            for (var i = 0; i < edges.length; i++) {
                var edge = edges[i];
                if (edge.GetFromState().equals(state) || edge.GetToState().equals(state)) {
                    var id = edge.GetId();
                    this.Edges.remove(id);
                }
            }
            this.States.remove(state.GetId());
        }
    };
    Graph.prototype.ReplaceState = function (old, _new) {
        if (old instanceof State) {
            old = old.GetId();
        }
        if (this.States.containsKey(old)) {
            var oldState = this.States.get(old);
            var edges = this.Edges.values();
            for (var i = 0; i < edges.length; i++) {
                var e = edges[i];
                if (e.GetFromState().equals(oldState)) {
                    e.SetFromState(_new);
                }
                if (e.GetToState().equals(oldState)) {
                    e.SetToState(_new);
                }
            }
            var id = old;
            _new.SetId(id);
            this.States.put(id, _new);
        }
    };
    Graph.prototype.ContainsState = function (state) {
        var id = state;
        if (state instanceof State) {
            id = state.GetId();
        }
        return this.States.containsKey(id);
    };
    // all edges pointing to the state
    Graph.prototype.GetFromNeighbours = function (state) {
        if (typeof state == "number") {
            state = this.States.get(state);
        }
        var result = [];
        var e = this.Edges.values();
        for (var i = 0; i < e.length; i++) {
            if (e[i].GetFromState().equals(state)) {
                result.push(e[i]);
            }
        }
        return result;
    };
    // edges leaving the state
    Graph.prototype.GetToNeighbours = function (state) {
        if (typeof state == "number") {
            state = this.States.get(state);
        }
        var result = [];
        var e = this.Edges.values();
        for (var i = 0; i < e.length; i++) {
            if (e[i].GetToState().equals(state) && !e[i].GetFromState().equals(state)) {
                result.push(e[i]);
            }
        }
        return result;
    };
    Graph.prototype.GetNeighbours = function (state) {
        if (typeof state == "number") {
            state = this.States.get(state);
        }
        var result = [];
        var e = this.Edges.values();
        for (var i = 0; i < e.length; i++) {
            if (e[i].GetFromState().equals(state) || e[i].GetToState().equals(state)) {
                result.push(e[i]);
            }
        }
        return result;
    };
    Graph.prototype.AddEdge = function (edge) {
        var id = edge.GetId();
        this.Edges.put(id, edge);
    };
    Graph.prototype.RemoveEdge = function (edge) {
        var id = typeof edge == "number" ? edge : edge.GetId();
        this.Edges.remove(id);
    };
    Graph.prototype.ContainsEdge = function (edge) {
        var id = edge;
        if (edge instanceof Edge) {
            id = edge.GetId();
        }
        return this.Edges.containsKey(id);
    };
    Graph.prototype.IsEmpty = function () {
        return this.States.isEmpty();
    };
    //#region Getters and Setters
    Graph.prototype.GetInitialState = function () {
        return this.Initial;
    };
    Graph.prototype.SetInitialState = function (state) {
        if (state == null) {
            this.Initial = undefined;
            return;
        }
        var s = state;
        if (typeof state == "number") {
            s = this.States.get(state);
        }
        this.Initial = s;
    };
    Graph.prototype.GetStates = function () {
        return this.States;
    };
    Graph.prototype.GetEdges = function () {
        return this.Edges;
    };
    Graph.prototype.GetState = function (id) {
        if (this.States.containsKey(id)) {
            return this.States.get(id);
        }
    };
    Graph.prototype.GetEdge = function (id) {
        if (this.Edges.containsKey(id)) {
            return this.Edges.get(id);
        }
    };
    return Graph;
}());
/// <reference path='./Edge.ts'/>
/// <reference path='./Graph.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../../Systems/Graph/index.ts'/>
var GraphToJson = /** @class */ (function () {
    function GraphToJson(graph) {
        this.Graph = graph;
    }
    GraphToJson.prototype.Convert = function () {
        var graph = this.Graph;
        var states = graph.GetStates().values();
        var edges = graph.GetEdges().values();
        var initial = graph.GetInitialState();
        var s = [];
        for (var i = 0; i < states.length; i++) {
            var state = states[i];
            var ss = state.ToSystemString();
            var sid = state.GetId();
            var j = new JSONState(ss, sid);
            s.push(j);
        }
        var e = [];
        for (var i = 0; i < edges.length; i++) {
            var edge = edges[i];
            var id = edge.GetId();
            var fid = edge.GetFromState().GetId();
            var tid = edge.GetToState().GetId();
            var j = new JSONEdge(id, fid, tid, edge.GetTransition());
            e.push(j);
        }
        var inij = undefined;
        if (initial != null) {
            var inid = initial.GetId();
            inij = new JSONInitialState(inid);
        }
        var obj = {};
        obj["states"] = s;
        obj["edges"] = e;
        obj["initial"] = inij;
        return JSON.stringify(obj);
    };
    return GraphToJson;
}());
var JSONState = /** @class */ (function () {
    function JSONState(s, id) {
        this.state = s;
        this.id = id;
    }
    return JSONState;
}());
var JSONEdge = /** @class */ (function () {
    function JSONEdge(id, fid, tid, t) {
        this.id = id;
        this.fromId = fid;
        this.toId = tid;
        this.transition = t;
    }
    return JSONEdge;
}());
var JSONInitialState = /** @class */ (function () {
    function JSONInitialState(id) {
        this.id = id;
    }
    return JSONInitialState;
}());
/// <reference path='./IConverter.ts'/>
/// <reference path='./GraphToJson.ts'/>
/// <reference path='./ResponseInterpreter.ts'/>
var URLGenerator = /** @class */ (function () {
    function URLGenerator(base) {
        this.Base = base;
        if (this.Base[this.Base.length - 1] != "/") {
            this.Base += "/";
        }
    }
    URLGenerator.prototype.GetURL = function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var res = args.join("/");
        return this.Base + res;
    };
    URLGenerator.GetURL = function (base) {
        var args = [];
        for (var _i = 1; _i < arguments.length; _i++) {
            args[_i - 1] = arguments[_i];
        }
        var gen = new URLGenerator(base);
        return gen.GetURL.apply(gen, args);
    };
    return URLGenerator;
}());
/// <reference path='./URLGenerator.ts'/>
/// <reference path='../Request/index.ts'/>
/// <reference path='../Modules/Converters/index.ts'/>
/// <reference path='../ResponseInterpreter/index.ts'/>
/// <reference path='../URLGenerator/index.ts'/>
var RequestStation = /** @class */ (function () {
    function RequestStation() {
    }
    RequestStation.RegisterUser = function (interpreter, data) {
        var generator = RequestStation.GetURLGenerator();
        var url = generator.GetURL("users", "new");
        var request = new AjaxRequest(interpreter, url, "post", true, data);
        request.Send();
    };
    RequestStation.RegisterPetrinet = function (interpreter, data) {
        var store = Store.GetInstance();
        var id = store.GetUserId();
        var generator = RequestStation.GetURLGenerator();
        var url = generator.GetURL("petrinet", id.toString(), "new");
        var request = new AjaxRequest(interpreter, url, "post", true, data);
        request.Send();
    };
    RequestStation.GetPetrinet = function (interpreter, id) {
        var generator = RequestStation.GetURLGenerator();
        var url = generator.GetURL("petrinet", id.toString());
        var request = new AjaxRequest(interpreter, url, "GET", true);
        request.Send();
    };
    RequestStation.GetPetrinetImage = function (interpreter, id) {
        var generator = RequestStation.GetURLGenerator();
        var url = generator.GetURL("petrinet", id.toString(), "image");
        var request = new AjaxRequest(interpreter, url, "GET", true);
        console.log(url);
        request.Send();
    };
    RequestStation.GetFeedback = function (interpreter, graph) {
        if (graph instanceof Graph) {
            graph = new GraphToJson(graph).Convert();
        }
        var uid = Store.GetInstance().GetUserId();
        var pid = Store.GetInstance().GetPetrinetId();
        var sid = Store.GetInstance().GetSessionId();
        var generator = RequestStation.GetURLGenerator();
        var url = generator.GetURL("petrinet", uid.toString(), pid.toString(), sid.toString(), "feedback");
        var request = new AjaxRequest(interpreter, url, "POST", true, graph, "application/json");
        request.Send();
    };
    RequestStation.SetSession = function (interpreter, uid, pid) {
        var generator = RequestStation.GetURLGenerator();
        var url = generator.GetURL("session", uid.toString(), pid.toString(), "new_session");
        var request = new AjaxRequest(interpreter, url, "POST", true);
        request.Send();
    };
    RequestStation.GetURLGenerator = function () {
        var path = Settings.GetInstance().GetApiPath();
        var generator = new URLGenerator(path);
        return generator;
    };
    return RequestStation;
}());
/// <reference path='./RequestStation.ts'/>
var UserCreatedResponse = /** @class */ (function () {
    function UserCreatedResponse() {
    }
    return UserCreatedResponse;
}());
var PetrinetCreatedResponse = /** @class */ (function () {
    function PetrinetCreatedResponse() {
    }
    return PetrinetCreatedResponse;
}());
var ErrorResponse = /** @class */ (function () {
    function ErrorResponse() {
    }
    return ErrorResponse;
}());
var PetrinetResponse = /** @class */ (function () {
    function PetrinetResponse() {
    }
    return PetrinetResponse;
}());
var SessionResponse = /** @class */ (function () {
    function SessionResponse() {
    }
    return SessionResponse;
}());
/// <reference path='Responses.ts'/>
var SVGParser = /** @class */ (function () {
    function SVGParser() {
    }
    /**
     * Creates a new svg element from a given svg-string
     * @param svgString The string containing the SVG element
     */
    SVGParser.ParseSvg = function (svgString) {
        var parser = new DOMParser();
        var img = parser.parseFromString(svgString, "image/svg+xml").documentElement;
        img.removeAttribute("width");
        img.removeAttribute("height");
        return img;
    };
    return SVGParser;
}());
/// <reference path='../HTMLGenerator.ts'/>
/// <reference path='./EventSupervisor.ts'/>
/// <reference path='./index.ts'/>
HTMLElement.prototype.replace = function (element) {
    console.log(this.outerHTML);
    console.log(element.outerHTML);
    this.outerHTML = element.outerHTML;
    // 'use-strict'; // For safari, and IE > 10
    // var parent = this.parentNode,
    //     i = arguments.length,
    //     firstIsNode = +(parent && typeof Ele === 'object');
    // if (!parent) return;
    // while (i-- > firstIsNode){
    //   if (parent && typeof arguments[i] !== 'object'){
    //     arguments[i] = document.createTextNode(arguments[i]);
    //   } if (!parent && arguments[i].parentNode){
    //     arguments[i].parentNode.removeChild(arguments[i]);
    //     continue;
    //   }
    //   parent.insertBefore(this.previousSibling, arguments[i]);
    // }
    // if (firstIsNode) parent.replaceChild(Ele, this);
};
/// <reference path='./EventSupervisor/EventSupervisor.ts'/>
/// <reference path='../Utils/Extensions/ChildNode.ts'/>
var HTMLGenerator = /** @class */ (function () {
    function HTMLGenerator() {
        this.Element = undefined;
        this.EventSupervisor = undefined;
    }
    HTMLGenerator.prototype.Generate = function () {
        var element = this.GenerateElement();
        if (this.ElementId)
            element.id = this.ElementId;
        if (this.ElementClassname)
            this.AddClassname(element, this.ElementClassname);
        return element;
    };
    HTMLGenerator.prototype.Render = function (force) {
        if (force === void 0) { force = false; }
        if (this.Element && !force)
            return this.Element;
        this.Element = this.Generate();
        if (this.EventSupervisor) {
            this.EventSupervisor.Register();
        }
        return this.Element;
    };
    HTMLGenerator.prototype.Remove = function () {
        if (this.Element) {
            this.Element.remove();
        }
    };
    HTMLGenerator.prototype.SetClassname = function (element, classname) {
        element.className = "";
        this.AddClassname(element, classname);
    };
    HTMLGenerator.prototype.AddClassname = function (element, classname) {
        var _a, _b;
        var prefixes = this.ClassPrefix.trim().split(' ');
        (_a = element.classList).add.apply(_a, prefixes);
        var classes = classname.trim().split(' ');
        (_b = element.classList).add.apply(_b, classes);
    };
    //#region Getters and setters
    HTMLGenerator.prototype.GetId = function () {
        return this.ElementId;
    };
    HTMLGenerator.prototype.SetId = function (id) {
        this.ElementId = id;
    };
    HTMLGenerator.prototype.GetClassname = function () {
        return this.ElementClassname;
    };
    HTMLGenerator.prototype.SetElementClassname = function (classname) {
        this.ElementClassname = classname;
        if (this.Element) {
            this.Element.className = "";
            this.AddClassname(this.Element, classname);
        }
    };
    HTMLGenerator.prototype.GetElement = function () {
        return this.Element;
    };
    return HTMLGenerator;
}());
/// <reference path='../HTMLGenerator.ts'/>
var ResizingHTMLGenerator = /** @class */ (function (_super) {
    __extends(ResizingHTMLGenerator, _super);
    function ResizingHTMLGenerator() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    ResizingHTMLGenerator.prototype.Generate = function () {
        var element = _super.prototype.Generate.call(this);
        window.addEventListener("resize", this.Resize.bind(this));
        return element;
    };
    return ResizingHTMLGenerator;
}(HTMLGenerator));
/// <reference path='./SplitContainer.ts'/>
/// <reference path='../../EventSupervisor/EventSupervisor.ts'/>
var SplitContainerSupervisor = /** @class */ (function () {
    function SplitContainerSupervisor(generator) {
        this.Generator = generator;
        this.LastDownTarget = undefined;
        this.MouseDown = false;
    }
    SplitContainerSupervisor.prototype.Register = function () {
        var _this = this;
        var element = this.Generator.Render();
        element.addEventListener("mousedown", function (e) {
            _this.MouseDown = true;
            _this.LastDownTarget = e.target;
        });
        element.addEventListener("mousemove", function (e) {
            if (_this.MouseDown && _this.LastDownTarget == _this.Generator.GetDivider()) {
                _this.Generator.MoveDivider(e.clientX);
            }
        });
        element.addEventListener("mouseup", function (e) {
            _this.MouseDown = false;
            _this.LastDownTarget = undefined;
        });
    };
    return SplitContainerSupervisor;
}());
/// <reference path='../ResizingHTMLGenerator.ts'/>
/// <reference path='./SplitContainerSupervisor.ts'/>
var SplitContainer = /** @class */ (function (_super) {
    __extends(SplitContainer, _super);
    function SplitContainer() {
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "__SPLIT_CONTAINER__";
        _this.ClassnameDivider = "divider";
        _this.ClassnameLeft = "left";
        _this.ClassnameRight = "right";
        _this.SetElementClassname("split");
        _this.DividerPosition = undefined;
        _this.DividerWidth = 20; //px
        _this.EventSupervisor = new SplitContainerSupervisor(_this);
        return _this;
    }
    SplitContainer.prototype.SetLeft = function (element) {
        this.Left = element;
        if (this.LeftElement) {
            if (element instanceof HTMLElement) {
                this.LeftElement.innerHTML = element.outerHTML;
            }
            else if (element instanceof HTMLGenerator) {
                var oh = element.Render().outerHTML;
                this.LeftElement.innerHTML = oh;
            }
        }
        else {
            var div = document.createElement("div");
            var e = element instanceof HTMLElement ? element : element.Render();
            div.appendChild(e);
            this.AddClassname(div, this.ClassnameLeft);
            this.LeftElement = div;
        }
    };
    SplitContainer.prototype.SetRight = function (element) {
        this.Right = element;
        if (this.RightElement) {
            if (element instanceof HTMLElement) {
                this.RightElement.innerHTML = element.outerHTML;
            }
            else if (element instanceof HTMLGenerator) {
                var oh = element.Render().outerHTML;
                this.RightElement.innerHTML = oh;
            }
        }
        else {
            var div = document.createElement("div");
            var e = element instanceof HTMLElement ? element : element.Render();
            div.appendChild(e);
            this.AddClassname(div, this.ClassnameRight);
            this.RightElement = div;
        }
    };
    SplitContainer.prototype.AppendLeft = function (element) {
        if (this.LeftElement) {
            this.LeftElement.appendChild(element);
        }
        else {
            this.SetLeft(element);
        }
    };
    SplitContainer.prototype.AppendRight = function (element) {
        if (this.RightElement) {
            this.RightElement.appendChild(element);
        }
        else {
            this.SetRight(element);
        }
    };
    SplitContainer.prototype.GetLeft = function () {
        return this.LeftElement;
    };
    SplitContainer.prototype.GetRight = function () {
        return this.RightElement;
    };
    SplitContainer.prototype.GetDivider = function () {
        if (!this.Divider) {
            var div = document.createElement("div");
            div.style.width = this.DividerWidth + "px";
            this.AddClassname(div, this.ClassnameDivider);
            this.Divider = div;
        }
        return this.Divider;
    };
    SplitContainer.prototype.MoveDivider = function (newx) {
        if (this.Element) {
            var parent_1 = this.Element.parentElement;
            if (newx >= 0 && newx + this.DividerWidth < parent_1.getBoundingClientRect().right) {
                var midx = this.DividerPosition + (this.DividerWidth / 2);
                var offset = newx - midx;
                var x = this.DividerPosition + offset + this.DividerWidth / 2;
                this.Divider.style.left = x.toString() + "px";
                this.LeftElement.style.width = newx.toString() + "px";
                this.RightElement.style.left = (newx + this.DividerWidth).toString() + "px";
                this.RightElement.style.width = (parent_1.getBoundingClientRect().width - newx - this.DividerWidth) + "px";
                this.DividerPosition = newx;
                if (this.Left instanceof ResizingHTMLGenerator) {
                    this.Left.Resize();
                }
                if (this.Right instanceof ResizingHTMLGenerator) {
                    this.Right.Resize();
                }
            }
        }
    };
    SplitContainer.prototype.GenerateElement = function () {
        var container = document.createElement("div");
        var left = this.GetLeft();
        var right = this.GetRight();
        var div = this.GetDivider();
        container.appendChild(left);
        container.appendChild(div);
        container.appendChild(right);
        return container;
    };
    SplitContainer.prototype.Resize = function () {
        var ow = this.GetLeft().getBoundingClientRect().width;
        ow += this.GetRight().getBoundingClientRect().width;
        ow += this.DividerWidth;
        var parent = this.Element.parentElement;
        var p = parent.getBoundingClientRect();
        var nw = p.width;
        var nh = p.height;
        var divfrac = this.DividerPosition / ow;
        this.DividerPosition = nw * divfrac;
        this.Element.style.width = nw.toString() + "px";
        this.Element.style.height = nh.toString() + "px";
        this.Divider.style.left = this.DividerPosition.toString() + "px";
        this.LeftElement.style.left = "0";
        this.LeftElement.style.width = (this.DividerPosition).toString() + "px";
        this.RightElement.style.left = (this.DividerPosition + this.DividerWidth).toString() + "px";
        this.RightElement.style.width = (nw - this.DividerPosition - this.DividerWidth).toString() + "px";
    };
    return SplitContainer;
}(ResizingHTMLGenerator));
/// <reference path='./SplitContainer.ts'/>
/// <reference path='./ResizingHTMLGenerator.ts'/>
/// <reference path='./SplitContainer/index.ts'/>
/// <reference path='./index.ts'/>
var HTMLInputGenerator = /** @class */ (function (_super) {
    __extends(HTMLInputGenerator, _super);
    function HTMLInputGenerator() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    return HTMLInputGenerator;
}(HTMLGenerator));
/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>
var SwitchState;
(function (SwitchState) {
    SwitchState[SwitchState["Off"] = 0] = "Off";
    SwitchState[SwitchState["On"] = 1] = "On";
})(SwitchState || (SwitchState = {}));
var Switcher = /** @class */ (function (_super) {
    __extends(Switcher, _super);
    function Switcher(toOn, toOff, start) {
        if (start === void 0) { start = SwitchState.Off; }
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "__SWITCHER__";
        _this.ToOnFunction = toOn;
        _this.ToOffFunction = toOff;
        _this.ClassnameOn = "on";
        _this.ClassnameOff = "off";
        _this.ClassnameLabel = "label";
        _this.OnLabel = "on";
        _this.OffLabel = "off";
        _this.Value = start;
        return _this;
    }
    Switcher.prototype.UpdateValue = function (newValue) {
        if (this.Value != newValue) {
            this.Toggle(true);
        }
    };
    Switcher.prototype.Toggle = function (external) {
        if (external === void 0) { external = false; }
        if (this.Value == SwitchState.Off) {
            if (!external) {
                this.ToOnFunction();
            }
            this.Value = SwitchState.On;
            this.SetElementClassname("on");
        }
        else {
            if (!external) {
                this.ToOffFunction();
            }
            this.Value = SwitchState.Off;
            this.SetElementClassname("off");
        }
    };
    Switcher.prototype.GenerateElement = function () {
        var _this = this;
        var element = document.createElement("div");
        if (this.Value == SwitchState.Off) {
            this.AddClassname(element, this.ClassnameOff);
        }
        else {
            this.AddClassname(element, this.ClassnameOn);
        }
        // left label
        var l = document.createElement("div");
        this.AddClassname(l, this.ClassnameLabel);
        this.AddClassname(l, this.ClassnameOn);
        l.appendChild(document.createTextNode(this.OffLabel));
        // right label
        var r = document.createElement("div");
        this.AddClassname(r, this.ClassnameLabel);
        this.AddClassname(r, this.ClassnameOff);
        r.appendChild(document.createTextNode(this.OnLabel));
        // actual switch
        var s = document.createElement("div");
        this.AddClassname(s, "switch");
        element.appendChild(l);
        element.appendChild(s);
        element.appendChild(r);
        s.addEventListener("click", function () {
            _this.Toggle();
        });
        return element;
    };
    return Switcher;
}(HTMLInputGenerator));
/// <reference path='./Switcher.ts'/>
/// <reference path='../HTMLGenerator.ts'/>
/// <reference path='./HTMLInputGenerator.ts'/>
/// <reference path='./Switcher/index.ts'/>
/// <reference path='./index.ts'/>
var Dialog = /** @class */ (function (_super) {
    __extends(Dialog, _super);
    function Dialog() {
        var _this = _super.call(this) || this;
        _this.Title = undefined;
        _this.Body = undefined;
        _this.ClassPrefix = "__DIALOG__";
        _this.ClassnameTitle = "title";
        _this.ClassnameBody = "body";
        return _this;
    }
    Dialog.prototype.GenerateElement = function () {
        var element = document.createElement("div");
        if (this.Title)
            element.appendChild(this.GetTitle());
        if (this.Body)
            element.appendChild(this.GetBody());
        return element;
    };
    //#region Getters and Setters
    Dialog.prototype.GetTitle = function () {
        return this.Title;
    };
    Dialog.prototype.SetTitle = function (title) {
        if (this.Title) {
            this.Title.textContent = title;
        }
        else {
            var element = document.createElement("h1");
            element.appendChild(document.createTextNode(title));
            this.AddClassname(element, this.ClassnameTitle);
            this.Title = element;
        }
    };
    Dialog.prototype.GetBody = function () {
        return this.Body;
    };
    Dialog.prototype.SetBody = function (body) {
        if (this.Body) {
            this.Body.innerHTML = body.outerHTML;
        }
        else {
            var wrapper = document.createElement("div");
            this.AddClassname(wrapper, this.ClassnameBody);
            wrapper.appendChild(body);
            this.Body = wrapper;
        }
    };
    Dialog.prototype.AppendBody = function (element) {
        if (this.Body) {
            this.Body.appendChild(element);
        }
        else {
            this.SetBody(element);
        }
    };
    Dialog.prototype.GetClassnameTitle = function () {
        return this.ClassnameTitle;
    };
    Dialog.prototype.SetClassnameTitle = function (name) {
        this.ClassnameTitle = name;
        if (this.Title) {
            this.SetClassname(this.Title, name);
        }
    };
    Dialog.prototype.GetClassnameBody = function () {
        return this.ClassnameBody;
    };
    Dialog.prototype.SetClassnameBody = function (name) {
        this.ClassnameBody = name;
        if (this.Body) {
            this.SetClassname(this.Body, name);
        }
    };
    return Dialog;
}(HTMLGenerator));
/// <reference path='../HTMLGenerator.ts'/>
/// <reference path='./Dialog.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../Dialog/index.ts'/>
var Popup = /** @class */ (function (_super) {
    __extends(Popup, _super);
    function Popup(left, top) {
        if (left === void 0) { left = undefined; }
        if (top === void 0) { top = undefined; }
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "__POPUP__";
        _this.ClassnameWrapper = "wrapper";
        _this.ClassnameBackdrop = "backdrop";
        _this.ClassnameDialog = "popup";
        _this.ClassnameCloseButton = "close";
        _this.Closeable = true;
        _this.Left = left;
        _this.Top = top;
        return _this;
    }
    Popup.prototype.GenerateElement = function () {
        var dialog = _super.prototype.GenerateElement.call(this);
        this.AddClassname(dialog, this.ClassnameDialog);
        this.Dialog = dialog;
        if (this.Top) {
            dialog.style.top = this.Top.toString() + "px";
        }
        if (this.Left) {
            dialog.style.left = this.Left.toString() + "px";
        }
        var wrapper = this.GenerateWrapper();
        var backdrop = this.GenerateBackdrop();
        wrapper.appendChild(backdrop);
        if (this.Closeable) {
            var button = this.GenerateCloseButton();
            dialog.appendChild(button);
            backdrop.addEventListener("click", this.Remove.bind(this));
        }
        wrapper.appendChild(dialog);
        return wrapper;
    };
    Popup.prototype.GenerateWrapper = function () {
        var wrapper = document.createElement("div");
        this.SetClassname(wrapper, this.ClassnameWrapper);
        return wrapper;
    };
    Popup.prototype.GenerateBackdrop = function () {
        var result = document.createElement("div");
        this.SetClassname(result, this.ClassnameBackdrop);
        return result;
    };
    Popup.prototype.GenerateCloseButton = function () {
        var button = document.createElement("span");
        button.appendChild(document.createTextNode("X"));
        button.addEventListener("click", this.Remove.bind(this));
        this.AddClassname(button, this.ClassnameCloseButton);
        return button;
    };
    //#region Getters and Setters
    Popup.prototype.GetCloseable = function () {
        return this.Closeable;
    };
    Popup.prototype.SetCloseable = function (b) {
        this.Closeable = b;
    };
    Popup.prototype.GetClassnameWrapper = function () {
        return this.ClassnameWrapper;
    };
    Popup.prototype.SetClassnameWrapper = function (name) {
        this.ClassnameWrapper = name;
    };
    Popup.prototype.GetClassnameBackdrop = function () {
        return this.ClassnameBackdrop;
    };
    Popup.prototype.SetClassnameBackdrop = function (name) {
        this.ClassnameBackdrop = name;
    };
    Popup.prototype.GetClassnameDialog = function () {
        return this.ClassnameDialog;
    };
    Popup.prototype.SetClassnameDialog = function (name) {
        this.ClassnameDialog = name;
    };
    Popup.prototype.GetClassnameCloseButton = function () {
        return this.ClassnameCloseButton;
    };
    Popup.prototype.SetClassnameCloseButton = function (name) {
        this.ClassnameCloseButton = name;
    };
    Popup.prototype.GetLeft = function () {
        return this.Left;
    };
    Popup.prototype.SetLeft = function (left) {
        this.Left = left;
        if (this.Element != null) {
            var d = this.Dialog;
            d.style.left = this.Left + "px";
        }
    };
    Popup.prototype.GetTop = function () {
        return this.Top;
    };
    Popup.prototype.SetTop = function (top) {
        this.Top = top;
        if (this.Element != null) {
            var d = this.Dialog;
            d.style.top = this.Top + "px";
        }
    };
    return Popup;
}(Dialog));
/// <reference path='./index.ts'/>
var ContextMenu = /** @class */ (function (_super) {
    __extends(ContextMenu, _super);
    function ContextMenu(left, top) {
        if (left === void 0) { left = 0; }
        if (top === void 0) { top = 0; }
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "__CONTEXT_MENU__";
        _this.Closeable = true;
        _this.MenuItems = [];
        _this.ElementId = "contextMenu";
        _this.SetClassnameBackdrop('menuBackDrop');
        _this.SetClassnameDialog('contextMenu');
        _this.SetClassnameTitle('titleDelete');
        _this.SetClassnameCloseButton("hide");
        _this.SetClassnameItem("item");
        _this.Body = undefined;
        _this.Top = top;
        _this.Left = left;
        return _this;
    }
    ContextMenu.prototype.Add = function (title, action) {
        this.MenuItems.push(new ContextMenuItem(title, action));
    };
    ContextMenu.prototype.GenerateElement = function () {
        for (var i = 0; i < this.MenuItems.length; i++) {
            var item = this.MenuItems[i];
            item.SetElementClassname(this.ItemClassname);
            this.AppendBody(item.Render());
        }
        var element = _super.prototype.GenerateElement.call(this);
        element.addEventListener("click", this.Remove.bind(this));
        return element;
    };
    ContextMenu.prototype.GetMenuItems = function () {
        return this.MenuItems;
    };
    ContextMenu.prototype.GetClassnameItem = function () {
        return this.ItemClassname;
    };
    ContextMenu.prototype.SetClassnameItem = function (name) {
        this.ItemClassname = name;
    };
    return ContextMenu;
}(Popup));
var ContextMenuItem = /** @class */ (function (_super) {
    __extends(ContextMenuItem, _super);
    function ContextMenuItem(text, action) {
        var _this = _super.call(this) || this;
        _this.Text = text;
        if (action)
            _this.Action = action;
        _this.ClassPrefix = "__CONTEXT_MENU_ITEM__";
        return _this;
    }
    ContextMenuItem.prototype.GenerateElement = function () {
        var item = document.createElement('div');
        item.appendChild(document.createTextNode(this.Text));
        item.addEventListener('click', this.Action);
        return item;
    };
    ContextMenuItem.prototype.getText = function () {
        return this.Text;
    };
    ContextMenuItem.prototype.setText = function (text) {
        this.Text = text;
    };
    ContextMenuItem.prototype.getAction = function () {
        return this.Action;
    };
    ContextMenuItem.prototype.setAction = function (action) {
        this.Action = action;
    };
    return ContextMenuItem;
}(HTMLGenerator));
/// <reference path='./ContextMenu.ts'/>
/// <reference path='./ContextMenuItem.ts'/>
/// <reference path='./Popup.ts'/>
/// <reference path='./ContextMenu/index.ts'/>
var QueueNode = /** @class */ (function () {
    function QueueNode(body, next) {
        this.body = body;
        this.next = next;
    }
    return QueueNode;
}());
/// <reference path='./QueueNode.ts'/>
var Queue = /** @class */ (function () {
    function Queue() {
        this.head = undefined;
        this.tail = undefined;
    }
    Queue.prototype.enqueue = function (x) {
        var node = new QueueNode(x, undefined);
        if (this.head == undefined) {
            this.head = node;
        }
        else {
            this.tail.next = node;
        }
        this.tail = node;
    };
    Queue.prototype.dequeue = function () {
        if (this.head != undefined) {
            var r = this.head.body;
            this.head = this.head.next;
            return r;
        }
        else {
            return undefined;
        }
    };
    Queue.prototype.isEmpty = function () {
        return this.head == undefined;
    };
    return Queue;
}());
/// <reference path='./index.ts'/>
/// <reference path='../../Utils/Datastructures/Queue/Queue.ts'/>
var FormBuilder = /** @class */ (function (_super) {
    __extends(FormBuilder, _super);
    function FormBuilder(submitOnEnter) {
        var _this = _super.call(this) || this;
        _this.FormElements = [];
        _this.Enctype = "multipart/form-data";
        _this.ClassPrefix = "__FORM__";
        _this.SubmitOnEnter = submitOnEnter;
        return _this;
    }
    FormBuilder.prototype.GenerateElement = function () {
        var form = document.createElement("form");
        form.enctype = this.Enctype;
        if (this.Method)
            form.method = this.Method;
        if (this.Action)
            form.action = this.Action;
        for (var i = 0; i < this.FormElements.length; i++) {
            form.appendChild(this.FormElements[i]);
        }
        return form;
    };
    FormBuilder.prototype.Generate = function () {
        var element = _super.prototype.Generate.call(this);
        if (!this.SubmitOnEnter) {
            this.PreventSubmit(element);
        }
        return element;
    };
    FormBuilder.prototype.PreventSubmit = function (element) {
        element.addEventListener("submit", function (e) {
            e.preventDefault();
        });
    };
    FormBuilder.prototype.AddElement = function (element) {
        this.FormElements.push(element);
    };
    FormBuilder.prototype.AddInput = function (name, type, placeholder) {
        var input = document.createElement("input");
        input.setAttribute("name", name);
        input.setAttribute("type", type);
        if (placeholder)
            input.setAttribute("placeholder", placeholder);
        this.AddElement(input);
        return input;
    };
    FormBuilder.prototype.AddTextArea = function (name, placeholder) {
        var area = document.createElement("textarea");
        area.setAttribute("name", name);
        if (placeholder)
            area.setAttribute("placeholder", placeholder);
        this.AddElement(area);
        return area;
    };
    FormBuilder.prototype.AddSelect = function (name, values) {
        var select = document.createElement("select");
        for (var i = 0; i < values.length; i++) {
            var value = values[i];
            var option = document.createElement("option");
            option.setAttribute("value", value);
            option.appendChild(document.createTextNode(value));
            select.appendChild(option);
        }
        select.setAttribute("name", name);
        this.AddElement(select);
    };
    FormBuilder.prototype.AddLabel = function (text, _for) {
        var label = document.createElement("label");
        label.appendChild(document.createTextNode(text));
        if (_for)
            label.setAttribute("for", _for);
        this.AddElement(label);
        return label;
    };
    FormBuilder.prototype.AddHTML = function (element) {
        this.AddElement(element);
    };
    //#region Getters and Setters
    FormBuilder.prototype.GetAction = function () {
        return this.Action;
    };
    FormBuilder.prototype.SetAction = function (action) {
        this.Action = action;
    };
    FormBuilder.prototype.GetMethod = function () {
        return this.Method;
    };
    FormBuilder.prototype.SetMethod = function (method) {
        this.Method = method;
    };
    FormBuilder.prototype.GetEnctype = function () {
        return this.Enctype;
    };
    FormBuilder.prototype.SetEnctype = function (enctype) {
        this.Enctype = enctype;
    };
    FormBuilder.prototype.GetSubmitOnEnter = function () {
        return this.SubmitOnEnter;
    };
    FormBuilder.prototype.SetSubmitOnEnter = function (submit) {
        this.SubmitOnEnter = submit;
    };
    return FormBuilder;
}(HTMLGenerator));
/// <reference path='../HTMLGenerator.ts'/>
/// <reference path='./FormBuilder.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../../Models/Settings.ts'/>
/// <reference path='../../Utils/Datastructures/Map/Map.ts'/>
var MenuBuilder = /** @class */ (function (_super) {
    __extends(MenuBuilder, _super);
    function MenuBuilder() {
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "__MENU__";
        _this.Categories = {};
        _this.ClassnameOpen = "open";
        _this.ClassnameClosed = "closed";
        _this.ClassnameCategory = "category";
        return _this;
    }
    MenuBuilder.GetInstance = function () {
        if (!MenuBuilder.Instance) {
            MenuBuilder.Instance = new MenuBuilder();
        }
        return MenuBuilder.Instance;
    };
    MenuBuilder.prototype.Update = function (settings) {
        var catKeys = Object.keys(this.Categories);
        for (var i = 0; i < catKeys.length; i++) {
            this.Categories[catKeys[i]].Update(settings);
        }
    };
    MenuBuilder.prototype.GenerateElement = function () {
        var menu = document.createElement("div");
        this.AddClassname(menu, this.ClassnameClosed);
        var catKeys = Object.keys(this.Categories);
        for (var i = 0; i < catKeys.length; i++) {
            var cat = this.Categories[catKeys[i]];
            menu.appendChild(cat.Render());
        }
        return menu;
    };
    MenuBuilder.prototype.AddCategory = function (name) {
        var cat = new MenuCategory(name);
        if (this.ClassnameCategory) {
            cat.SetElementClassname(this.ClassnameCategory);
        }
        this.Categories[name] = cat;
    };
    MenuBuilder.prototype.AddMenuItem = function (category, binding, item) {
        if (!this.Categories[category]) {
            this.AddCategory(category);
        }
        this.Categories[category].AddItem(binding, item);
    };
    MenuBuilder.prototype.Open = function () {
        var element = this.Element;
        element.classList.remove(this.ClassnameClosed);
        element.classList.add(this.ClassnameOpen);
    };
    MenuBuilder.prototype.Close = function () {
        var element = this.Element;
        element.classList.remove(this.ClassnameOpen);
        element.classList.add(this.ClassnameClosed);
    };
    MenuBuilder.prototype.Toggle = function () {
        var element = this.Element;
        if (element.classList.contains(this.ClassnameOpen)) {
            this.Close();
        }
        else {
            this.Open();
        }
    };
    return MenuBuilder;
}(HTMLGenerator));
/// <reference path='./index.ts'/>
var MenuCategory = /** @class */ (function (_super) {
    __extends(MenuCategory, _super);
    function MenuCategory(name) {
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "__MENU_CATEGORY__";
        _this.Name = name;
        _this.MenuItems = {};
        _this.ClassnameHeader = "header";
        _this.ClassnameList = "list";
        return _this;
    }
    MenuCategory.prototype.GenerateElement = function () {
        var container = document.createElement("div");
        var header = document.createElement("div");
        header.appendChild(document.createTextNode(this.Name));
        if (this.ClassnameHeader)
            this.AddClassname(header, this.ClassnameHeader);
        container.appendChild(header);
        var menuItemKeys = Object.keys(this.MenuItems);
        if (menuItemKeys.length > 0) {
            var list = document.createElement('ul');
            if (this.ClassnameList)
                this.AddClassname(list, this.ClassnameList);
            ;
            for (var i = 0; i < menuItemKeys.length; i++) {
                list.appendChild(this.MenuItems[menuItemKeys[i]].Render());
            }
            container.appendChild(list);
        }
        return container;
    };
    MenuCategory.prototype.AddItem = function (binding, item) {
        item.SetBinding(binding);
        this.MenuItems[binding] = item;
    };
    MenuCategory.prototype.Update = function (settings) {
        var keys = Object.keys(settings);
        for (var i = 0; i < keys.length; i++) {
            if (this.MenuItems[keys[i]]) {
                this.MenuItems[keys[i]].Update(settings);
            }
        }
    };
    //#region Getters and Setters
    MenuCategory.prototype.GetName = function () {
        return this.Name;
    };
    MenuCategory.prototype.SetName = function (name) {
        this.Name = name;
    };
    MenuCategory.prototype.GetClassnameHeader = function () {
        return this.ClassnameHeader;
    };
    MenuCategory.prototype.SetClassnameHeader = function (name) {
        this.ClassnameHeader = name;
    };
    MenuCategory.prototype.GetClassnameList = function () {
        return this.ClassnameList;
    };
    MenuCategory.prototype.SetClassnameList = function (name) {
        this.ClassnameList = name;
    };
    return MenuCategory;
}(HTMLGenerator));
/// <reference path='./index.ts'/>
/// <reference path='../HTMLGenerator.ts'/>
/// <reference path='../HTMLInputGenerator/index.ts'/>
var MenuItem = /** @class */ (function (_super) {
    __extends(MenuItem, _super);
    function MenuItem(description, body) {
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "_MENU_ELEMENT__";
        _this.Description = description;
        if (body) {
            _this.Option = body;
        }
        _this.ClassnameDescription = "description";
        _this.ClassnameOptionBody = "option";
        return _this;
    }
    MenuItem.prototype.GenerateElement = function () {
        var element = document.createElement("li");
        var desc = document.createElement("div");
        this.AddClassname(desc, this.ClassnameDescription);
        desc.appendChild(document.createTextNode(this.Description));
        element.appendChild(desc);
        if (this.Option) {
            var option = document.createElement("div");
            this.AddClassname(option, this.ClassnameOptionBody);
            option.appendChild(this.Option.Render());
            element.appendChild(option);
        }
        return element;
    };
    MenuItem.prototype.Update = function (settings) {
        if (this.Option) {
            var val = settings[this.Binding];
            this.Option.UpdateValue(val);
        }
    };
    //#region Getters and Setters
    MenuItem.prototype.GetDescription = function () {
        return this.Description;
    };
    MenuItem.prototype.SetDescription = function (desc) {
        this.Description = desc;
    };
    MenuItem.prototype.GetOption = function () {
        return this.Option;
    };
    MenuItem.prototype.SetOption = function (body) {
        this.Option = body;
    };
    MenuItem.prototype.GetBinding = function () {
        return this.Binding;
    };
    MenuItem.prototype.SetBinding = function (binding) {
        this.Binding = binding;
    };
    return MenuItem;
}(HTMLGenerator));
/// <reference path='../HTMLInputGenerator/index.ts'/>
/// <reference path='../HTMLGenerator.ts'/>
/// <reference path='./MenuBuilder.ts'/>
/// <reference path='./MenuCategory.ts'/>
/// <reference path='./MenuItem.ts'/>
/// <reference path='./MenuOption.ts'/>
/// <reference path='./index.ts'/>
var ParagraphBuilder = /** @class */ (function (_super) {
    __extends(ParagraphBuilder, _super);
    function ParagraphBuilder() {
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "PARAGRAPH_BUILDER";
        _this.Items = [];
        return _this;
    }
    ParagraphBuilder.prototype.Add = function (a) {
        if (typeof a == "string") {
            a = document.createTextNode(a);
        }
        this.Items.push(a);
    };
    ParagraphBuilder.prototype.Clear = function () {
        this.Items = [];
        this.Element = null;
    };
    ParagraphBuilder.prototype.GenerateElement = function () {
        var p = document.createElement("p");
        for (var i = 0; i < this.Items.length; i++) {
            p.appendChild(this.Items[i]);
        }
        return p;
    };
    return ParagraphBuilder;
}(HTMLGenerator));
/// <reference path='../HTMLGenerator.ts'/>
/// <reference path='./ParagraphBuilder.ts'/>
// base
/// <reference path='./HTMLGenerator.ts'/>
// abstract subs
/// <reference path='./ResizingHTMLGenerators/index.ts'/>
/// <reference path='./HTMLInputGenerator/index.ts'/>
// concrete
/// <reference path='./Dialog/index.ts'/>
/// <reference path='./Popup/index.ts'/>
/// <reference path='./FormBuilder/index.ts'/>
/// <reference path='./MenuBuilder/index.ts'/>
/// <reference path='./ParagraphBuilder/index.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../../../HTMLGenerators/EventSupervisor/index.ts'/>
var Modeller = /** @class */ (function () {
    function Modeller(drawer) {
        this.Drawer = drawer;
        this.MouseIsDown = false;
        this.LastDownTarget = undefined;
        this.Register();
    }
    Modeller.prototype.Register = function () {
        var _this = this;
        var element = this.Drawer.Render();
        window.addEventListener('mousedown', function (e) {
            _this.LastDownTarget = e.target;
        });
        element.addEventListener("mousedown", function (e) {
            _this.MouseIsDown = true;
        });
        element.addEventListener("mouseup", function (e) {
            _this.MouseIsDown = false;
        });
    };
    return Modeller;
}());
var Point = /** @class */ (function () {
    function Point(x, y) {
        this.X = x;
        this.Y = y;
    }
    Point.prototype.ToString = function () {
        var s = "(";
        s += this.X.toString();
        s += ", ";
        s += this.Y.toString();
        s += ")";
        return s;
    };
    return Point;
}());
/// <reference path='./index.ts'/>
/// <reference path='../../../HTMLGenerators/index.ts'/>
/// <reference path='../../../Utils/Datastructures/Point/Point.ts'/>
var Drawer = /** @class */ (function (_super) {
    __extends(Drawer, _super);
    function Drawer(width, height) {
        var _this = _super.call(this) || this;
        _this.CanvasWidth = width;
        _this.CanvasHeight = height;
        return _this;
    }
    Drawer.prototype.GenerateElement = function () {
        var canvas = document.createElement("canvas");
        canvas.setAttribute("width", this.CanvasWidth.toString());
        canvas.setAttribute("height", this.CanvasHeight.toString());
        return canvas;
    };
    Drawer.prototype.Resize = function () {
        if (this.Element) {
            var parent_2 = this.Element.parentElement;
            var p = parent_2.getBoundingClientRect();
            this.CanvasWidth = p.width;
            this.CanvasHeight = p.height;
            var element = this.Element;
            element.setAttribute("width", this.CanvasWidth.toString());
            element.setAttribute("height", this.CanvasHeight.toString());
        }
    };
    Drawer.prototype.ClearCanvas = function () {
        var context = this.Element.getContext("2d");
        context.clearRect(0, 0, this.CanvasWidth, this.CanvasHeight);
    };
    Drawer.prototype.TranslatePointFromViewportToCanvas = function (point) {
        var actualSize = this.Element.getBoundingClientRect();
        var horizontalFrac = this.CanvasWidth / actualSize.width;
        var verticalFrac = this.CanvasHeight / actualSize.height;
        var newx = Math.round(point.X * horizontalFrac);
        var newy = Math.round(point.Y * verticalFrac);
        var res = new Point(newx, newy);
        return res;
    };
    Drawer.prototype.TranslatePointFromCanvasToScreen = function (point) {
        var actualSize = this.Element.getBoundingClientRect();
        var horizontalFrac = actualSize.width / this.CanvasWidth;
        var verticalFrac = actualSize.height / this.CanvasHeight;
        var newx = Math.round(point.X * horizontalFrac);
        var newy = Math.round(point.Y * verticalFrac);
        var res = new Point(newx, newy);
        return res;
    };
    Drawer.prototype.SnapPointToGrid = function (position) {
        var settings = Settings.GetInstance();
        var hs = settings.GetHorizontalSteps();
        var vs = settings.GetVerticalSteps();
        var s = this.CanvasWidth / hs;
        var t = this.CanvasHeight / vs;
        var p = position;
        var h = Math.round(p.X / s);
        var v = Math.round(p.Y / t);
        p.X = h * s;
        p.Y = v * t;
        return p;
    };
    Drawer.prototype.DrawGrid = function () {
        var settings = Settings.GetInstance();
        var enabled = settings.GetDisplayGrid();
        if (enabled) {
            var context = this.Element.getContext('2d');
            var hsteps = settings.GetHorizontalSteps();
            var vsteps = settings.GetVerticalSteps();
            var sw = this.CanvasWidth / hsteps;
            var sh = this.CanvasHeight / vsteps;
            context.save();
            context.beginPath();
            context.lineWidth = 1;
            context.strokeStyle = "#ccc";
            // vertical lines
            for (var i = 0; i < hsteps; i++) {
                context.moveTo(i * sw, 0);
                context.lineTo(i * sw, this.CanvasHeight);
            }
            // horizontal lines
            for (var i = 0; i < vsteps; i++) {
                context.moveTo(0, i * sh);
                context.lineTo(this.CanvasWidth, i * sh);
            }
            context.closePath();
            context.stroke();
            context.restore();
        }
    };
    return Drawer;
}(ResizingHTMLGenerator));
/// <reference path='./Modeller.ts'/>
/// <reference path='./Drawer.ts'/>
/// <reference path='../Utils/Datastructures/Point/Point.ts'/>
/// <reference path='../Datastructures/Point/Point.ts'/>
Math.radToDeg = function (radians) {
    return radians / (Math.PI / 180) % 360;
};
Math.degToRad = function (degrees) {
    return degrees * (Math.PI / 180) % (2 * Math.PI);
};
Math.CalcHorizontalDistance = function (a, b) {
    var res = Math.abs(a.X - b.X);
    return res;
};
Math.CalcVerticalDistance = function (a, b) {
    var res = Math.abs(a.Y - b.Y);
    return res;
};
Math.calcAngle = function (from, to) {
    var dx = Math.CalcHorizontalDistance(from, to);
    var dy = Math.CalcVerticalDistance(from, to);
    if (dx == 0) {
        var k = 0.5 * Math.PI;
        if (from.Y < to.Y) {
            k += Math.PI;
        }
        return k;
    }
    var theta = Math.atan(dy / dx);
    if (from.Y < to.Y) // top to bottom
        theta *= -1;
    if (from.X >= to.X) // right to left
        theta = Math.PI - theta;
    if (to.X > from.X && from.Y < to.Y)
        theta = (2 * Math.PI) - Math.abs(theta);
    return theta;
};
var Size = /** @class */ (function () {
    function Size(w, h) {
        this.Width = w;
        this.Height = h;
    }
    return Size;
}());
/// <reference path='../Utils/Extensions/Math.ts'/>
/// <reference path='../Utils/Datastructures/Size/Size.ts'/>
/// <reference path='../Utils/Datastructures/Point/Point.ts'/>
var Shape = /** @class */ (function () {
    function Shape() {
    }
    Shape.prototype.Fill = function (context) {
        context.save();
        context.beginPath();
        this.FillShape(context);
        context.closePath();
        context.restore();
    };
    Shape.prototype.Stroke = function (context) {
        context.save();
        context.beginPath();
        this.StrokeShape(context);
        context.closePath();
        context.restore();
    };
    return Shape;
}());
/// <reference path='./Shape.ts'/>
var OnePointShape = /** @class */ (function (_super) {
    __extends(OnePointShape, _super);
    function OnePointShape(p) {
        var _this = _super.call(this) || this;
        _this.StartPoint = p;
        return _this;
    }
    return OnePointShape;
}(Shape));
/// <reference path='./OnePointShape.ts'/>
var TwoPointShape = /** @class */ (function (_super) {
    __extends(TwoPointShape, _super);
    function TwoPointShape(a, b) {
        var _this = _super.call(this, a) || this;
        _this.EndPoint = b;
        return _this;
    }
    TwoPointShape.prototype.GetSize = function () {
        var s = new Size(Math.CalcHorizontalDistance(this.StartPoint, this.EndPoint), Math.CalcVerticalDistance(this.StartPoint, this.EndPoint));
        return s;
    };
    TwoPointShape.prototype.GetMidPoint = function () {
        var tl = new Point(Math.min(this.StartPoint.X, this.EndPoint.X), Math.min(this.StartPoint.Y, this.EndPoint.Y));
        var br = new Point(Math.max(this.StartPoint.X, this.EndPoint.X), Math.max(this.StartPoint.Y, this.EndPoint.Y));
        var dx = Math.CalcHorizontalDistance(tl, br);
        var dy = Math.CalcVerticalDistance(tl, br);
        var mid = new Point(tl.X + dx / 2, tl.Y + dy / 2);
        return mid;
    };
    return TwoPointShape;
}(OnePointShape));
/// <reference path='./TwoPointShape.ts'/>
var ThreePointShape = /** @class */ (function (_super) {
    __extends(ThreePointShape, _super);
    function ThreePointShape(a, b, c) {
        var _this = _super.call(this, a, b) || this;
        _this.ThirdPoint = c;
        return _this;
    }
    return ThreePointShape;
}(TwoPointShape));
/// <reference path='./TwoPointShape.ts'/>
var Line = /** @class */ (function (_super) {
    __extends(Line, _super);
    function Line(a, b) {
        var _this = _super.call(this, a, b) || this;
        _this.Curvature = 0;
        return _this;
    }
    Line.prototype.SetPath = function (ctx) {
        var c = this.GetCurvePoint();
        ctx.bezierCurveTo(this.StartPoint.X, this.StartPoint.Y, c.X, c.Y, this.EndPoint.X, this.EndPoint.Y);
    };
    Line.prototype.FillShape = function (ctx) {
        this.SetPath(ctx);
        ctx.fill();
    };
    Line.prototype.StrokeShape = function (ctx) {
        this.SetPath(ctx);
        ctx.stroke();
    };
    Line.prototype.Hit = function (context, point, strokeWidth) {
        if (strokeWidth === void 0) { strokeWidth = 15; }
        context.beginPath();
        this.SetPath(context);
        context.lineWidth = strokeWidth;
        var res = context.isPointInStroke(point.X, point.Y);
        context.closePath();
        return res;
    };
    Line.prototype.GetMidPoint = function () {
        var start = this.StartPoint;
        var end = this.EndPoint;
        var dx = end.X - start.X;
        var dy = end.Y - start.Y;
        var mx = start.X + (dx / 2);
        var my = start.Y + (dy / 2);
        var m = new Point(mx, my);
        return m;
    };
    Line.prototype.GetCurvePoint = function () {
        var m = this.GetMidPoint();
        var angle = Math.calcAngle(this.StartPoint, this.EndPoint);
        var newAngle = (-angle + (0.5 * Math.PI)) % (2 * Math.PI);
        var px = m.X + this.Curvature * Math.cos(newAngle);
        var py = m.Y + this.Curvature * Math.sin(newAngle);
        var p = new Point(px, py);
        return p;
    };
    Line.prototype.GetCurveCutPoint = function () {
        var m = this.GetMidPoint();
        var angle = Math.calcAngle(this.StartPoint, this.EndPoint);
        var newAngle = (-angle + (0.5 * Math.PI)) % (2 * Math.PI);
        var px = m.X + (this.Curvature / ((3 / 4) * Math.PI)) * Math.cos(newAngle);
        var py = m.Y + (this.Curvature / ((3 / 4) * Math.PI)) * Math.sin(newAngle);
        var p = new Point(px, py);
        return p;
    };
    return Line;
}(TwoPointShape));
/// <reference path='./TwoPointShape.ts'/>
var Box = /** @class */ (function (_super) {
    __extends(Box, _super);
    function Box(a, b) {
        var _this = this;
        var tl = new Point(Math.min(a.X, b.X), Math.min(a.Y, b.Y));
        var br = new Point(Math.max(a.X, b.X), Math.max(a.Y, b.Y));
        _this = _super.call(this, tl, br) || this;
        return _this;
    }
    Box.prototype.FillShape = function (context) {
        this.SetPath(context);
        context.fill();
    };
    Box.prototype.StrokeShape = function (context) {
        this.SetPath(context);
        context.stroke();
    };
    Box.prototype.SetPath = function (ctx) {
        ctx.rect(this.StartPoint.X, this.StartPoint.Y, Math.CalcHorizontalDistance(this.StartPoint, this.EndPoint), Math.CalcVerticalDistance(this.StartPoint, this.EndPoint));
    };
    Box.prototype.Hit = function (context, p) {
        return p.X >= this.StartPoint.X &&
            p.X <= this.EndPoint.X &&
            p.Y >= this.StartPoint.Y &&
            p.Y <= this.EndPoint.Y;
    };
    Box.prototype.GetBoundingPoint = function (angle) {
        var size = this.GetSize();
        var midPoint = new Point(this.StartPoint.X + (size.Width / 2), this.StartPoint.Y + (size.Height / 2));
        var trAngle = Math.calcAngle(midPoint, new Point(this.EndPoint.X, this.StartPoint.Y));
        var tlAngle = Math.calcAngle(midPoint, this.StartPoint);
        var blAngle = Math.calcAngle(midPoint, new Point(this.StartPoint.X, this.EndPoint.Y));
        var brAngle = Math.calcAngle(midPoint, this.EndPoint);
        var qx, qy = 0;
        // angle is on left or right side
        if (angle < trAngle || angle > brAngle ||
            angle > tlAngle && angle < blAngle) {
            var knownSide = size.Width / 2;
            var v = knownSide * Math.tan(angle);
            if (angle > tlAngle && angle <= blAngle) {
                knownSide *= -1;
                v *= -1;
            }
            qx = midPoint.X + knownSide;
            qy = midPoint.Y - v;
        }
        // angle is on top or bottom side.
        else {
            var knownSide = size.Height / 2;
            var v = knownSide * (1 / Math.tan(angle));
            if (angle > blAngle && angle < brAngle) {
                knownSide *= -1;
                v *= -1;
            }
            qx = midPoint.X + v;
            qy = midPoint.Y - knownSide;
        }
        return new Point(qx, qy);
    };
    Box.prototype.GetAngles = function () {
        var result = [
            this.GetTopRightAngle(),
            this.GetTopLeftAngle(),
            this.GetBottomLeftAngle(),
            this.GetBottomRightAngle()
        ];
        return result;
    };
    Box.prototype.GetTopRightAngle = function () {
        var s = this.GetSize();
        var w = s.Width / 2;
        var h = s.Height / 2;
        return Math.atan(h / w);
    };
    Box.prototype.GetTopLeftAngle = function () {
        var a = this.GetTopRightAngle();
        a = Math.PI - a;
        return a;
    };
    Box.prototype.GetBottomLeftAngle = function () {
        var a = this.GetTopRightAngle();
        a += Math.PI;
        return a;
    };
    Box.prototype.GetBottomRightAngle = function () {
        var a = this.GetTopRightAngle();
        a = (2 * Math.PI) - a;
        return a;
    };
    return Box;
}(TwoPointShape));
var BoxSide;
(function (BoxSide) {
    BoxSide[BoxSide["top"] = 0] = "top";
    BoxSide[BoxSide["right"] = 1] = "right";
    BoxSide[BoxSide["bottom"] = 2] = "bottom";
    BoxSide[BoxSide["left"] = 3] = "left";
})(BoxSide || (BoxSide = {}));
/// <reference path='./ThreePointShape.ts'/>
var Triangle = /** @class */ (function (_super) {
    __extends(Triangle, _super);
    function Triangle(a, b, c) {
        return _super.call(this, a, b, c) || this;
    }
    Triangle.prototype.FillShape = function (context) {
        this.SetPath(context);
        context.fill();
    };
    Triangle.prototype.StrokeShape = function (context) {
        this.SetPath(context);
        context.stroke();
    };
    Triangle.prototype.SetPath = function (ctx) {
        ctx.beginPath();
        ctx.moveTo(this.StartPoint.X, this.StartPoint.Y);
        ctx.lineTo(this.ThirdPoint.X, this.ThirdPoint.Y);
        ctx.lineTo(this.EndPoint.X, this.EndPoint.Y);
        ctx.lineTo(this.StartPoint.X, this.StartPoint.Y);
        ctx.closePath();
    };
    Triangle.prototype.Hit = function (context, p) {
        return false;
    };
    return Triangle;
}(ThreePointShape));
/// <reference path='./Line.ts'/>
/// <reference path='./Triangle.ts'/>
var Arrow = /** @class */ (function (_super) {
    __extends(Arrow, _super);
    function Arrow(a, b) {
        var _this = _super.call(this, a, b) || this;
        _this.TipWidth = 15;
        _this.TipHeight = 15;
        return _this;
    }
    Arrow.prototype.StrokeShape = function (context) {
        _super.prototype.StrokeShape.call(this, context);
        var tip = this.GetArrowTip();
        tip.Stroke(context);
    };
    Arrow.prototype.FillShape = function (context) {
        _super.prototype.StrokeShape.call(this, context);
        var tip = this.GetArrowTip();
        tip.Fill(context);
        tip.Stroke(context);
    };
    Arrow.prototype.GetArrowTip = function () {
        var width = this.TipWidth;
        var height = this.TipHeight;
        var angle = Math.calcAngle(this.GetCurvePoint(), this.EndPoint);
        var mx = this.EndPoint.X - height * Math.cos(angle);
        var my = this.EndPoint.Y + height * Math.sin(angle);
        var w = width / 2;
        var theta = (angle + 0.5 * Math.PI) % (2 * Math.PI);
        var ax = mx + w * Math.cos(theta);
        var ay = my - w * Math.sin(theta);
        var bx = mx - w * Math.cos(theta);
        var by = my + w * Math.sin(theta);
        return new Triangle(new Point(ax, ay), new Point(bx, by), this.EndPoint);
    };
    return Arrow;
}(Line));
/// <reference path='./index.ts'/>
var Circle = /** @class */ (function (_super) {
    __extends(Circle, _super);
    function Circle(position, radius, start, end) {
        var _this = _super.call(this, position) || this;
        _this.Radius = radius;
        _this.StartRadian = start;
        _this.EndRadian = end;
        return _this;
    }
    Circle.prototype.SetPath = function (context) {
        context.arc(this.StartPoint.X, this.StartPoint.Y, this.Radius, this.StartRadian, this.EndRadian);
    };
    Circle.prototype.StrokeShape = function (context) {
        this.SetPath(context);
        context.stroke();
    };
    Circle.prototype.FillShape = function (context) {
        this.SetPath(context);
        context.fill();
    };
    Circle.prototype.Hit = function (context, position) {
        var padding = 10;
        var dx = Math.CalcHorizontalDistance(position, this.StartPoint);
        var dy = Math.CalcVerticalDistance(position, this.StartPoint);
        return Math.sqrt(dx * dx + dy * dy) <= this.Radius + padding;
    };
    return Circle;
}(OnePointShape));
/// <reference path='./Shape.ts'/>
/// <reference path='./OnePointShape.ts'/>
/// <reference path='./TwoPointShape.ts'/>
/// <reference path='./ThreePointShape.ts'/>
/// <reference path='./Line.ts'/>
/// <reference path='./Box.ts'/>
/// <reference path='./Triangle.ts'/>
/// <reference path='./Arrow.ts'/>
/// <reference path='./Circle.ts'/>
/// <reference path='../Shapes/index.ts'/>
var Drawing = /** @class */ (function () {
    function Drawing() {
    }
    Drawing.prototype.Draw = function (context) {
        context.save();
        this.DrawShape(context);
        context.restore();
    };
    Drawing.prototype.Hit = function (context, point) {
        context.save();
        var shape = this.GetShape(context);
        var res = shape.Hit(context, point);
        context.restore();
        return res;
    };
    return Drawing;
}());
/// <reference path='./IMoveableDrawing.ts'/>
/// <reference path='./Drawing.ts'/>
var Pair = /** @class */ (function () {
    function Pair(a, b) {
        this.Item1 = a;
        this.Item2 = b;
    }
    return Pair;
}());
/// <reference path='./index.ts'/>
/// <reference path='../Modeller/index.ts'/>
/// <reference path='../../../Models/Store.ts'/>
/// <reference path='../../../Systems/index.ts'/>
/// <reference path='../../../Drawings/index.ts'/>
/// <reference path='../../../HTMLGenerators/Popup/index.ts'/>
/// <reference path='../../../Utils/Datastructures/Pair/Pair.ts'/>
/// <reference path='../../../../vendor/Definitions/Hashtable.d.ts'/>
var GraphDrawer = /** @class */ (function (_super) {
    __extends(GraphDrawer, _super);
    function GraphDrawer(width, height) {
        if (width === void 0) { width = 500; }
        if (height === void 0) { height = 500; }
        var _this = _super.call(this, width, height) || this;
        _this.StateDrawings = new Hashtable();
        _this.EdgeDrawings = new Hashtable();
        _this.SelfLoopDrawings = new Hashtable();
        _this.StyleManager = new StyleManager();
        return _this;
    }
    GraphDrawer.prototype.Draw = function (selected, feedback) {
        if (!this.Element)
            return; // can't draw yet
        // // clear the canvas and draw grid
        this.ClearCanvas();
        this.DrawGrid();
        if (feedback != null) {
            this.StyleManager.SetFeedback(feedback);
        }
        var context = this.Element.getContext('2d');
        var stateDrawings = this.StateDrawings.values();
        var edgeDrawings = this.EdgeDrawings.values();
        var drawnEdges = new Hashtable(); // avoid redrawing
        var seperationDistance = Settings.GetInstance().GetSeperationDistance();
        var store = Store.GetInstance();
        // // draw initial state pointer
        if (store.GetGraph().GetInitialState()) {
            context.save();
            context.lineWidth = 2;
            var s = store.GetGraph().GetInitialState();
            var id = s.GetId();
            var drawing = this.StateDrawings.get(id);
            var position = drawing.GetPosition();
            var k = new Point(position.X - 40, position.Y - 50);
            var arrow = new Arrow(k, position);
            arrow.TipHeight = 20;
            arrow.TipWidth = 10;
            arrow.Fill(context);
            context.restore();
        }
        // // draw self loops
        var selfLoopDrawings = this.SelfLoopDrawings.values();
        for (var i = 0; i < selfLoopDrawings.length; i++) {
            context.save();
            var drawing = selfLoopDrawings[i];
            this.StyleManager.SetSelfLoopStyle(drawing.GetEdge().GetId(), context);
            drawing.Draw(context);
            if (drawing.GetEdge().GetId() == selected) {
                StyleManager.SetSelectedSelfLoopStyle(context);
                drawing.Draw(context);
            }
            StyleManager.SetStandardSelfLoopStyle(context);
            drawing.Draw(context);
            context.restore();
        }
        var _loop_1 = function (i) {
            var sd = stateDrawings[i];
            var _loop_2 = function (j) {
                // get the amount of edges between the two states
                // console.log(edgeDrawings);
                var sharedEdges = edgeDrawings.filter(function (edge) {
                    return edge.GetFromDrawing() == stateDrawings[i] && edge.GetToDrawing() == stateDrawings[j] ||
                        edge.GetFromDrawing() == stateDrawings[j] && edge.GetToDrawing() == stateDrawings[i];
                });
                // draw these edges
                for (var k = 0; k < sharedEdges.length; k++) {
                    var edgeDrawing = sharedEdges[k];
                    if (!drawnEdges.get(edgeDrawing.GetEdge().GetId())) {
                        var c = 0;
                        if (sharedEdges.length > 1) {
                            c = (k * seperationDistance) - ((seperationDistance * (sharedEdges.length - 1)) / 2);
                        }
                        if (edgeDrawing.GetFromDrawing() == sd) {
                            c = -c;
                        }
                        context.save();
                        edgeDrawing.SetCurvature(c);
                        this_1.StyleManager.SetEdgeStyle(edgeDrawing.GetEdge().GetId(), context);
                        edgeDrawing.Draw(context);
                        if (edgeDrawing.GetEdge().GetId() == selected) {
                            StyleManager.SetSelectedEdgeStyle(context);
                            edgeDrawing.Draw(context);
                        }
                        StyleManager.SetStandardEdgeStyle(context);
                        edgeDrawing.Draw(context); //, edgeDrawing.GetEdge().GetId() == selectedId);
                        drawnEdges.put(edgeDrawing.GetEdge().GetId(), true);
                        context.restore();
                    }
                }
            };
            for (var j = 0; j < stateDrawings.length; j++) {
                _loop_2(j);
            }
        };
        var this_1 = this;
        // draw edges
        for (var i = 0; i < stateDrawings.length; i++) {
            _loop_1(i);
        }
        // draw states
        context.save();
        for (var i = 0; i < stateDrawings.length; i++) {
            var sd = stateDrawings[i];
            this.StyleManager.SetStateStyle(sd.GetState().GetId(), context);
            sd.Draw(context);
            // StyleManager.SetStandardStateStyle(context);
            if (sd.GetState().GetId() == selected) {
                StyleManager.SetSelectedStateStyle(context);
                sd.Draw(context);
            }
            StyleManager.SetStandardStateStyle(context);
            sd.Draw(context);
        }
        context.restore();
    };
    // protected ProcessFeedback(feedback : Feedback)
    // {
    //     let keys = feedback.SpecificItems.keys();
    //     for(let i = 0; i < keys.length; i++) {
    //         let drawing = this.GetDrawing(keys[i]);
    //         let code = feedback.SpecificItems.get(keys[i]);
    //         drawing.AddFeedback(feedback.GetFeedback(keys[i]));            
    //     }
    // }
    GraphDrawer.prototype.AddStateDrawing = function (sd) {
        var id = sd.GetState().GetId();
        this.StateDrawings.put(id, sd);
    };
    GraphDrawer.prototype.RemoveStateDrawing = function (id) {
        if (this.StateDrawings.containsKey(id)) {
            var drawing = this.StateDrawings.get(id);
            var skeys = this.SelfLoopDrawings.keys();
            for (var i = 0; i < skeys.length; i++) {
                var key = skeys[i];
                if (this.SelfLoopDrawings.get(key).GetFromDrawing() == drawing) {
                    this.SelfLoopDrawings.remove(key);
                }
            }
            var ekeys = this.EdgeDrawings.keys();
            for (var i = 0; i < ekeys.length; i++) {
                var key = ekeys[i];
                if (this.EdgeDrawings.get(key).GetFromDrawing() == drawing ||
                    this.EdgeDrawings.get(key).GetToDrawing() == drawing) {
                    this.EdgeDrawings.remove(key);
                }
            }
            this.StateDrawings.remove(id);
        }
    };
    GraphDrawer.prototype.GetStateDrawing = function (id) {
        if (this.StateDrawings.containsKey(id)) {
            return this.StateDrawings.get(id);
        }
    };
    GraphDrawer.prototype.AddEdgeDrawing = function (d) {
        var id = d.GetEdge().GetId();
        var from = d.GetEdge().GetFromState();
        var to = d.GetEdge().GetToState();
        if (from.equals(to)) {
            this.SelfLoopDrawings.put(id, d);
        }
        else {
            this.EdgeDrawings.put(id, d);
        }
    };
    GraphDrawer.prototype.RemoveEdgeDrawing = function (id) {
        if (this.SelfLoopDrawings.containsKey(id)) {
            this.SelfLoopDrawings.remove(id);
        }
        if (this.EdgeDrawings.containsKey(id)) {
            this.EdgeDrawings.remove(id);
        }
    };
    GraphDrawer.prototype.GetEdgeDrawing = function (id) {
        if (this.EdgeDrawings.containsKey(id)) {
            return this.EdgeDrawings.get(id);
        }
        else if (this.SelfLoopDrawings.containsKey(id)) {
            return this.SelfLoopDrawings.get(id);
        }
    };
    // public GetDrawing(id : number):             GraphElementDrawing
    // {
    //     let res : GraphElementDrawing = this.GetStateDrawing(id);
    //     if(!res) {
    //         res = this.GetEdgeDrawing(id);
    //     }
    //     return res;
    // }
    GraphDrawer.prototype.GetMoveableDrawing = function (id) {
        var res;
        if (this.StateDrawings.containsKey(id)) {
            res = this.StateDrawings.get(id);
        }
        if (this.SelfLoopDrawings.containsKey(id)) {
            res = this.SelfLoopDrawings.get(id);
        }
        return res;
    };
    GraphDrawer.prototype.HitDrawing = function (position) {
        if (!this.Element)
            return; // false;
        var context = this.Element.getContext("2d");
        var sd = this.StateDrawings.keys();
        for (var i = 0; i < sd.length; i++) {
            if (this.StateDrawings.get(sd[i]).Hit(context, position)) {
                return sd[i];
            }
        }
        var ekeys = this.EdgeDrawings.keys();
        for (var i = 0; i < ekeys.length; i++) {
            if (this.EdgeDrawings.get(ekeys[i]).Hit(context, position)) {
                return ekeys[i];
            }
        }
        var skeys = this.SelfLoopDrawings.keys();
        for (var i = 0; i < skeys.length; i++) {
            if (this.SelfLoopDrawings.get(skeys[i]).Hit(context, position)) {
                return skeys[i];
            }
        }
        return undefined;
    };
    GraphDrawer.prototype.Resize = function () {
        _super.prototype.Resize.call(this);
        var settings = Settings.GetInstance();
        var snap = settings.GetSnapGrid();
        if (snap) {
            var stateDrawings = this.StateDrawings.values();
            for (var i = 0; i < stateDrawings.length; i++) {
                var drawing = stateDrawings[i];
                var p = drawing.GetPosition();
                this.SnapPointToGrid(p);
                drawing.MoveTo(p);
            }
        }
        this.Draw();
    };
    GraphDrawer.prototype.Update = function (settings) {
        if (this.Element)
            this.Draw();
    };
    return GraphDrawer;
}(Drawer));
var Stack = /** @class */ (function () {
    function Stack() {
        this.Top = undefined;
    }
    Stack.prototype.Push = function (x) {
        var node = new StackNode(x, this.Top);
        this.Top = node;
    };
    Stack.prototype.Pop = function () {
        var res = undefined;
        if (this.Top) {
            res = this.Top.Key;
            this.Top = this.Top.Prev;
        }
        return res;
    };
    Stack.prototype.IsEmpty = function () {
        return this.Top == undefined;
    };
    return Stack;
}());
var StackNode = /** @class */ (function () {
    function StackNode(key, prev) {
        this.Key = key;
        this.Prev = prev;
    }
    return StackNode;
}());
var VisitState;
(function (VisitState) {
    VisitState[VisitState["Unvisited"] = 0] = "Unvisited";
    VisitState[VisitState["Tovisit"] = 1] = "Tovisit";
    VisitState[VisitState["Visited"] = 2] = "Visited";
})(VisitState || (VisitState = {}));
/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>
/// <reference path='../../Converters/index.ts'/>
/// <reference path='../../../Drawings/index.ts'/>
/// <reference path='../../../Utils/Datastructures/Point/Point.ts'/>
/// <reference path='../../../Utils/Datastructures/Size/Size.ts'/>
/// <reference path='../../../Utils/Datastructures/Stack/Stack.ts'/>
var GraphModeller = /** @class */ (function (_super) {
    __extends(GraphModeller, _super);
    function GraphModeller(drawer) {
        var _this = _super.call(this, drawer) || this;
        _this.InitialPoint = undefined;
        _this.MouseOffset = undefined;
        _this.MousePos = undefined;
        _this.CurrentMenu = undefined;
        _this.SelectedId = undefined;
        _this.History = new HistoryList();
        _this.FeedbackContainer = new FeedbackContainer(_this.Drawer.GetElement().parentElement);
        _this.Tutorial = new Tutorial(document.body);
        return _this;
    }
    GraphModeller.prototype.Register = function () {
        var _this = this;
        _super.prototype.Register.call(this);
        var modeller = this.Drawer;
        var element = modeller.GetElement();
        window.addEventListener('keydown', function (e) {
            _this.KeyPress(e);
        });
        element.addEventListener("contextmenu", function (e) {
            _this.RightClick(e);
        });
        element.addEventListener("mousedown", function (e) {
            _this.MouseDown(e);
        });
        element.addEventListener("mousemove", function (e) {
            _this.MouseMove(e);
        });
        element.addEventListener("mouseup", function (e) {
            _this.MouseUp(e);
        });
        element.addEventListener("dblclick", function (e) {
            _this.DoubleClick(e);
        });
    };
    GraphModeller.prototype.GenerateButtons = function () {
        var _this = this;
        var addStateButton = new AddStateButton();
        var button = addStateButton.Render();
        button.addEventListener("click", function (e) {
            _this.AddState();
        });
        var p = this.Drawer.GetElement().parentElement;
        p.appendChild(button);
        this.AddStateButton = button;
        var feedbackButton = new FeedbackButton();
        button = feedbackButton.Render();
        button.addEventListener("click", function (e) {
            if (_this.Feedback.isEmpty()) {
                _this.GetFeedback();
            }
            else {
                _this.ClearFeedback();
            }
        });
        p.appendChild(button);
        this.FeedbackButton = feedbackButton;
        this.Update(Settings.GetInstance());
    };
    GraphModeller.prototype.ExecuteAndStore = function (action) {
        action.Invoke();
        this.History.Add(action);
        var settings = Settings.GetInstance();
        if (settings.GetDifficulty() == ModelingDifficulty.NOVICE) {
            this.GetFeedback();
        }
        else if (settings.GetDifficulty() == ModelingDifficulty.ADVANCED) {
            this.ClearFeedback();
        }
    };
    GraphModeller.prototype.Undo = function () {
        if (!this.History.IsEmpty()) {
            this.History.Undo();
            var settings = Settings.GetInstance();
            if (settings.GetDifficulty() == ModelingDifficulty.NOVICE) {
                this.GetFeedback();
            }
            else if (settings.GetDifficulty() == ModelingDifficulty.ADVANCED) {
                this.ClearFeedback();
            }
            this.DrawGraph();
        }
    };
    GraphModeller.prototype.Redo = function () {
        this.History.Redo();
        var settings = Settings.GetInstance();
        if (settings.GetDifficulty() == ModelingDifficulty.NOVICE) {
            this.GetFeedback();
        }
        else if (settings.GetDifficulty() == ModelingDifficulty.ADVANCED) {
            this.ClearFeedback();
        }
        this.DrawGraph();
    };
    GraphModeller.prototype.DrawGraph = function () {
        this.Drawer.Draw(this.SelectedId, this.Feedback);
    };
    //#region graph manipulation
    GraphModeller.prototype.AddState = function (position) {
        if (position === void 0) { position = undefined; }
        var action = new AddState(this.Drawer, "", position);
        this.ExecuteAndStore(action);
        this.DrawGraph();
        this.HideMenu();
    };
    GraphModeller.prototype.SetInitial = function () {
        var graph = Store.GetInstance().GetGraph();
        var initial = graph.GetInitialState();
        // null checks to prevent duplicate initial state setters
        if ((this.SelectedId != null && initial == null && graph.ContainsState(this.SelectedId)) ||
            (initial != null && this.SelectedId != null && initial.GetId() != this.SelectedId && graph.ContainsState(this.SelectedId)) ||
            (initial != null && this.SelectedId == null)) {
            var a = new SetInitialState(this.Drawer, this.SelectedId);
            this.ExecuteAndStore(a);
            this.DrawGraph();
        }
        this.HideMenu();
    };
    GraphModeller.prototype.RemoveElement = function () {
        if (this.SelectedId == null)
            return;
        var id = this.SelectedId;
        var graph = Store.GetInstance().GetGraph();
        var a;
        if (graph.ContainsEdge(id)) {
            a = new RemoveEdge(this.Drawer, id);
        }
        else if (graph.ContainsState(id)) {
            a = new RemoveState(this.Drawer, id);
        }
        this.ExecuteAndStore(a);
        this.DrawGraph();
        this.SelectedId = null;
        this.HideMenu();
    };
    //#endregion
    //#region SettingsObserver
    // Update on Settings Change
    GraphModeller.prototype.Update = function (s) {
        var diff = s.GetDifficulty();
        if (diff == ModelingDifficulty.NOVICE) {
            this.GetFeedback();
            if (this.FeedbackButton != null) {
                this.FeedbackButton.GetElement().classList.add("disabled");
            }
        }
        else if (diff == ModelingDifficulty.ADVANCED) {
            this.ClearFeedback();
            if (this.FeedbackButton != null) {
                this.FeedbackButton.GetElement().classList.remove("disabled");
            }
        }
    };
    //endregion
    //#region Feedback
    GraphModeller.prototype.ClearFeedback = function () {
        this.Feedback = new Feedback();
        this.FeedbackContainer.SetFeedback(this.Feedback);
        this.FeedbackButton.SetFeedback(this.Feedback);
        this.DrawGraph();
    };
    GraphModeller.prototype.GetFeedback = function () {
        var graph = Store.GetInstance().GetGraph();
        if (!graph.IsEmpty()) {
            var graphstring = new GraphToJson(graph).Convert();
            RequestStation.GetFeedback(this, graph);
        }
    };
    GraphModeller.prototype.ReceiveBusy = function () {
        if (this.FeedbackButton != null) {
            var button = this.FeedbackButton;
            button.SetBusy();
        }
    };
    GraphModeller.prototype.ReceiveSuccess = function (code, responseText) {
        try {
            this.Feedback = Feedback.JsonToFeedback(responseText);
            this.FeedbackContainer.SetFeedback(this.Feedback);
            if (Settings.GetInstance().GetDifficulty() == ModelingDifficulty.NOVICE) {
                this.FeedbackContainer.SetFeedback(this.Feedback);
                this.FeedbackContainer.Display(this.Drawer.GetElement().parentElement);
            }
            else {
                this.FeedbackButton.SetFeedback(this.Feedback);
            }
            this.DrawGraph();
        }
        catch (e) {
            console.log("could not parse feedback");
        }
    };
    GraphModeller.prototype.ReceiveFailure = function (code, responseText) {
        try {
            var e = JSON.parse(responseText);
            console.log(e);
        }
        catch (ex) {
            console.log(responseText);
        }
    };
    GraphModeller.prototype.PrintFeedback = function () {
        var f = this.Feedback;
        var general = f.GeneralItems.values();
        console.log(general);
        var skeys = f.SpecificItems.keys().sort();
        for (var i = 0; i < skeys.length; i++) {
            var id = skeys[i];
            var codes = f.SpecificItems.get(id).Items.values();
            console.log(id, codes);
        }
    };
    GraphModeller.prototype.Attach = function (irp) {
        this.SubInterpreters.push(irp);
    };
    GraphModeller.prototype.Detach = function (irp) {
        var index = this.SubInterpreters.indexOf(irp);
        if (index >= 0) {
            this.SubInterpreters.removeAt(this.SubInterpreters.indexOf(irp));
        }
    };
    //endregion
    //#region Events
    GraphModeller.prototype.MouseDown = function (e) {
        var petrinet = Store.GetInstance().GetPetrinet();
        this.InitialPoint = this.Drawer.TranslatePointFromViewportToCanvas(new Point(e.offsetX, e.offsetY));
        if (!e.ctrlKey) {
            this.SelectedId = this.Drawer.HitDrawing(this.InitialPoint);
            var drawing = this.GetSelectedDrawing();
            if (drawing != null && drawing instanceof StateDrawing) {
                this.MouseOffset = new Size(this.InitialPoint.X - drawing.GetPosition().X, this.InitialPoint.Y - drawing.GetPosition().Y);
            }
        }
        else if (this.SelectedId != null) {
            var transitions = petrinet.GetTransitions().sort();
            var t0 = transitions[0];
            var otherId = this.Drawer.HitDrawing(this.InitialPoint);
            if (otherId != null) {
                var a = new AddEdge(this.Drawer, this.SelectedId, otherId, t0);
                this.ExecuteAndStore(a);
            }
        }
    };
    GraphModeller.prototype.MouseMove = function (e) {
        this.MousePos = new Point(e.offsetX, e.offsetY);
        var p = new Point(e.offsetX, e.offsetY);
        p = this.Drawer.TranslatePointFromViewportToCanvas(p);
        var k = this.Drawer.HitDrawing(p);
        if (k != null) {
            this.FeedbackContainer.SetElementId(k);
        }
        else if (this.Feedback != null &&
            !this.Feedback.GeneralItems.contains(FeedbackCode.INCORRECT_INITIAL_STATE) &&
            !this.Feedback.GeneralItems.contains(FeedbackCode.NO_INITIAL_STATE)) {
            this.FeedbackContainer.Remove();
        }
        if (this.MouseIsDown && this.InitialPoint && !e.ctrlKey) {
            var drawing = this.GetSelectedDrawing();
            var p_1 = this.Drawer.TranslatePointFromViewportToCanvas(new Point(e.offsetX, e.offsetY));
            if (drawing != null) {
                if (this.MouseOffset) {
                    p_1.X -= this.MouseOffset.Width;
                    p_1.Y -= this.MouseOffset.Height;
                }
                if (Settings.GetInstance().GetSnapGrid() && drawing instanceof StateDrawing) {
                    p_1 = this.Drawer.SnapPointToGrid(p_1);
                }
                drawing.MoveTo(p_1);
                this.DrawGraph();
            }
        }
    };
    GraphModeller.prototype.RightClick = function (e) {
        e.preventDefault();
        // simulate left click
        this.MouseDown(e);
        this.MouseUp(e);
        var position = new Point(e.pageX, e.pageY);
        this.ShowContextMenu(position, new Point(e.offsetX, e.offsetY));
        this.MouseIsDown = false;
    };
    GraphModeller.prototype.DoubleClick = function (e) {
        this.EditElementMenu();
    };
    GraphModeller.prototype.MouseUp = function (e) {
        this.InitialPoint = undefined;
        this.MouseOffset = undefined;
        this.DrawGraph();
    };
    GraphModeller.prototype.KeyPress = function (e) {
        switch (e.keyCode) {
            case 72:
                {
                    this.ToggleTutorial();
                }
        }
        // if a menu is not open
        if (this.LastDownTarget == this.Drawer.GetElement() && !this.CurrentMenu) {
            switch (e.keyCode) {
                case 46: // delete
                    {
                        this.RemoveElement();
                        break;
                    }
                case 65: // a
                    {
                        var p = this.MousePos;
                        var action = new AddState(this.Drawer, "", p);
                        this.ExecuteAndStore(action);
                        this.DrawGraph();
                        break;
                    }
                case 69: //e
                    {
                        e.preventDefault();
                        this.EditElementMenu();
                        break;
                    }
                case 73: // i
                    {
                        this.SetInitial();
                        break;
                    }
                case 89: // y
                    {
                        if (e.ctrlKey) {
                            this.Redo();
                        }
                        break;
                    }
                case 90: // z
                    {
                        if (e.ctrlKey) {
                            if (!e.shiftKey) {
                                this.Undo();
                            }
                            else {
                                this.Redo();
                            }
                        }
                        break;
                    }
            }
        }
        else if (this.LastDownTarget == this.Drawer.GetElement() && this.CurrentMenu != null) {
            switch (e.keyCode) {
                case 27: // esc
                    {
                        this.HideMenu();
                        break;
                    }
            }
        }
    };
    //#endregion
    //#region Menus
    GraphModeller.prototype.ShowMenu = function (menu) {
        this.HideMenu(); // hide the current menu
        this.CurrentMenu = menu;
        this.CurrentMenu.Show();
        this.CurrentMenu.Focus();
    };
    GraphModeller.prototype.HideMenu = function () {
        if (this.CurrentMenu) {
            this.CurrentMenu.Remove();
            this.CurrentMenu = undefined;
            if (this.Drawer.GetElement() != null) {
                this.LastDownTarget = this.Drawer.GetElement();
            }
        }
    };
    GraphModeller.prototype.EditElementMenu = function () {
        if (this.SelectedId != null) {
            var sd = this.Drawer.GetStateDrawing(this.SelectedId);
            if (sd != null) {
                this.ShowEditStateMenu(sd);
                return;
            }
            var ed = this.Drawer.GetEdgeDrawing(this.SelectedId);
            if (ed != null) {
                this.ShowEditEdgeMenu(ed);
            }
        }
    };
    GraphModeller.prototype.ToggleTutorial = function () {
        // let t = new Tutorial(document.body);
        var t = this.Tutorial;
        t.Toggle();
    };
    GraphModeller.prototype.ShowEditStateMenu = function (sd) {
        var menu = new EditStateMenu(this, sd);
        this.ShowMenu(menu);
        menu.SetLeft();
        menu.SetTop();
    };
    GraphModeller.prototype.ShowEditEdgeMenu = function (ed) {
        var menu = new EditEdgeMenu(this, ed);
        this.ShowMenu(menu);
    };
    GraphModeller.prototype.ShowContextMenu = function (p, rp) {
        var menu = new GraphModellerContextMenu(this, p, rp);
        this.ShowMenu(menu);
    };
    //#endregion
    //#region Helpers
    GraphModeller.prototype.GetSelectedDrawing = function () {
        if (this.SelectedId != null)
            return this.Drawer.GetMoveableDrawing(this.SelectedId);
    };
    //#endregion
    GraphModeller.prototype.GetDrawer = function () {
        return this.Drawer;
    };
    return GraphModeller;
}(Modeller));
var GraphModellerMenu = /** @class */ (function () {
    function GraphModellerMenu(m) {
        this.Modeller = m;
        this.Element = undefined;
    }
    GraphModellerMenu.prototype.Show = function () {
        this.Element = this.GetElement();
        var parent = this.GetParent();
        parent.appendChild(this.Element);
    };
    GraphModellerMenu.prototype.Remove = function () {
        if (this.Element != null) {
            this.Element.remove();
        }
    };
    GraphModellerMenu.prototype.Hide = function () {
        this.Modeller.HideMenu();
    };
    return GraphModellerMenu;
}());
// /// <reference path='./index.ts'/>
// class StateMenu extends GraphModellerMenu
// {
//     public Drawing : StateDrawing;
//     public constructor(modeller : GraphDrawer, sd : StateDrawing)
//     {
//         super(modeller);
//         this.Drawing = sd;
//     }
//     protected GetElement()
//     {
//         let p = new Popup();
//         let context = this.Modeller.GetElement().getContext("2d");
//         let drawing = this.Drawing;
//         let pos     = drawing.GetPosition();
//         let size    = drawing.GetSize(context);
//         let settings = Settings.GetInstance();
//         let stateHeight = settings.GetStateHeight();
//         let buttons = document.createElement("div");
//         buttons.classList.add("buttons");
//         let editButton = document.createElement("button");
//         editButton.classList.add("confirm");
//         editButton.appendChild(document.createTextNode("Edit"));
//         let connectButton = document.createElement("button");
//         connectButton.classList.add("confirm");
//         connectButton.appendChild(document.createTextNode("Connect"));
//         buttons.appendChild(editButton);
//         buttons.appendChild(connectButton);
//         p.SetBody(buttons);
//         p.SetLeft(pos.X + size.Width / 2);
//         p.SetTop(pos.Y + stateHeight + 5);
//         p.SetElementClassname("editState");
//         return p.Render();
//     }
//     protected GetParent()
//     {
//         return this.Modeller.GetElement().parentElement;
//     }
// }
// // class StateMenu extends ModellerMenu<GraphModeller>
// // {
// //     public constructor(modeller : GraphModeller, sd : StateDrawing)
// //     {
// //         super(modeller);
// //         let context = this.Modeller.GetElement().getContext("2d");
// //         let drawing = sd;
// //         let pos     = drawing.GetPosition();
// //         let size    = drawing.GetSize(context);
// //         let state   = drawing.GetState();
// //         let store   = Store.GetInstance();
// //         let net     = store.GetPetrinet();
// //         let places  = net.GetPlaces().sort();
// //         let settings = Settings.GetInstance();
// //         let sh = settings.GetStateHeight();
// //         let buttons = document.createElement("div");
// //         buttons.classList.add("buttons");
// //         let editButton = document.createElement("button");
// //         editButton.classList.add("confirm");
// //         editButton.appendChild(document.createTextNode("Edit"));
// //         let connectButton = document.createElement("button");
// //         connectButton.classList.add("confirm");
// //         connectButton.appendChild(document.createTextNode("Connect"));
// //         buttons.appendChild(editButton);
// //         buttons.appendChild(connectButton);
// //         this.SetBody(buttons);
// //         this.SetLeft(pos.X + size.Width / 2);
// //         this.SetTop(pos.Y + sh + 5);
// //         this.SetElementClassname("editState");
// //     }
// // }
// /// <reference path='./index.ts'/>
// /// <reference path='../../../../Models/index.ts'/>
// /// <reference path='../../../../Modules/Modellers/index.ts'/>
// /// <reference path='../../../../Drawings/index.ts'/>
// /// <reference path='../../../../HTMLGenerators/index.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>
var EditStateMenu = /** @class */ (function (_super) {
    __extends(EditStateMenu, _super);
    // private Size    : Size;
    function EditStateMenu(modeller, sd) {
        var _this = _super.call(this, modeller) || this;
        _this.Drawing = sd;
        _this.Inputs = [];
        return _this;
    }
    EditStateMenu.prototype.Focus = function () {
        if (this.Element == null)
            return;
        this.Inputs[0].focus();
        this.Inputs[0].select();
    };
    EditStateMenu.prototype.SetLeft = function (left) {
        var drawing = this.Drawing;
        var context = this.Modeller.GetDrawer().GetElement().getContext("2d");
        var state = drawing.GetState();
        var pos = drawing.GetPosition();
        var size = drawing.GetSize(context);
        var container = this.Modeller.GetDrawer().GetElement();
        if (left != null) {
            this.Popup.SetLeft(left);
            return;
        }
        var p = this.Popup;
        var rect = p.GetBody().getBoundingClientRect();
        var crect = container.getBoundingClientRect();
        left = Math.max(pos.X + size.Width / 2, rect.width / 2);
        left = Math.min(left, crect.width - rect.width / 2);
        p.SetLeft(left);
    };
    EditStateMenu.prototype.SetTop = function (top) {
        var drawing = this.Drawing;
        var context = this.Modeller.GetDrawer().GetElement().getContext("2d");
        var state = drawing.GetState();
        var pos = drawing.GetPosition();
        var size = drawing.GetSize(context);
        var sh = Settings.GetInstance().GetStateHeight();
        var container = this.Modeller.GetDrawer().GetElement();
        if (top != null) {
            this.Popup.SetTop(top);
            return;
        }
        var p = this.Popup;
        var rect = this.Popup.GetBody().getBoundingClientRect();
        var crect = container.getBoundingClientRect();
        top = pos.Y + sh + 5;
        if (rect.height + top > crect.height) {
            top = pos.Y - rect.height - 15;
        }
        p.SetTop(top);
        // p.SetTop(pos.Y + sh + 5);
    };
    EditStateMenu.prototype.Confirm = function () {
        var store = Store.GetInstance();
        var net = store.GetPetrinet();
        var places = net.GetPlaces().sort();
        var sd = this.Drawing;
        var state = sd.GetState();
        var s = "";
        var k = "";
        for (var i = 0; i < this.Inputs.length - 1; i++) {
            k = this.ParsePlaceValue(this.Inputs[i].value);
            s += places[i] + ":" + k + ",";
        }
        k = this.ParsePlaceValue(this.Inputs[this.Inputs.length - 1].value);
        s += places[this.Inputs.length - 1] + ":" + k;
        var a = new EditState(this.Modeller.GetDrawer(), this.Drawing.GetState(), s);
        this.Modeller.ExecuteAndStore(a);
        this.Modeller.Drawer.Draw();
    };
    EditStateMenu.prototype.ParsePlaceValue = function (s) {
        var k = s;
        k.trim();
        if (k == "") {
            k = "0";
        }
        return k;
    };
    EditStateMenu.prototype.GetElement = function () {
        var _this = this;
        var p = new Popup();
        var context = this.Modeller.GetDrawer().GetElement().getContext("2d");
        var drawing = this.Drawing;
        var state = drawing.GetState();
        var store = Store.GetInstance();
        var net = store.GetPetrinet();
        var places = net.GetPlaces().sort();
        var inputs = this.Inputs;
        var placesContainer = document.createElement("div");
        placesContainer.classList.add("places");
        var _loop_3 = function (i) {
            // container
            var e_1 = document.createElement("div");
            e_1.classList.add("place");
            // place text
            var place = document.createElement("div");
            place.appendChild(document.createTextNode(places[i]));
            place.classList.add("name");
            // input
            var inp = document.createElement("input");
            inp.classList.add("input");
            var val = state.GetPlace(places[i]);
            if (val == null)
                val = new IntToken(0);
            inp.setAttribute("type", "text");
            inp.setAttribute("value", val.ToString());
            inp.addEventListener("keydown", function (e) {
                if (e.keyCode == 9 || e.which == 9) { // tab
                    e.preventDefault();
                    if (!e.shiftKey) {
                        var j = i + 1;
                        if (j >= inputs.length)
                            j = 0;
                        inputs[j].focus();
                        inputs[j].select();
                    }
                    else {
                        var j = i - 1;
                        if (j < 0)
                            j = inputs.length - 1;
                        inputs[j].focus();
                        inputs[j].select();
                    }
                }
                if (e.keyCode == 13 || e.which == 13) { // enter
                    _this.Confirm();
                    _this.Hide();
                }
            });
            inputs[i] = inp;
            var buttonrow = document.createElement("div");
            buttonrow.classList.add("buttons");
            var minusButton = document.createElement("button");
            minusButton.appendChild(document.createTextNode("-"));
            minusButton.addEventListener("click", function (e) {
                var current = parseInt(inp.value);
                if (isNaN(current)) {
                    inp.value = "0";
                }
                else {
                    var s = Math.max(Number(inp.value) - 1, 0);
                    inp.value = s.toString();
                }
            });
            var omegaButton = document.createElement("button");
            omegaButton.appendChild(document.createTextNode("ω"));
            omegaButton.addEventListener("click", function (e) {
                inp.value = "ω";
            });
            var plusButton = document.createElement("button");
            plusButton.appendChild(document.createTextNode("+"));
            plusButton.addEventListener("click", function (e) {
                var current = parseInt(inp.value);
                if (isNaN(current)) {
                    inp.value = "1";
                }
                else {
                    var s = Math.max(Number(inp.value) + 1, 0);
                    inp.value = s.toString();
                }
            });
            buttonrow.appendChild(minusButton);
            buttonrow.appendChild(omegaButton);
            buttonrow.appendChild(plusButton);
            e_1.appendChild(place);
            e_1.appendChild(inp);
            e_1.appendChild(buttonrow);
            placesContainer.appendChild(e_1);
        };
        for (var i = 0; i < places.length; i++) {
            _loop_3(i);
        }
        var buttonContainer = document.createElement("div");
        buttonContainer.classList.add("buttons");
        var confirmButton = document.createElement("button");
        confirmButton.appendChild(document.createTextNode("Change"));
        confirmButton.classList.add("confirm");
        confirmButton.addEventListener("click", function (e) {
            _this.Confirm();
            _this.Hide();
        });
        var cancelButton = document.createElement("button");
        cancelButton.appendChild(document.createTextNode("Cancel"));
        cancelButton.classList.add("cancel");
        cancelButton.addEventListener("click", function (e) {
            _this.Hide();
        });
        buttonContainer.appendChild(cancelButton);
        buttonContainer.appendChild(confirmButton);
        buttonContainer.classList.add("buttons");
        p.SetBody(placesContainer);
        p.AppendBody(buttonContainer);
        p.SetElementClassname("edit state");
        this.Popup = p;
        var e = p.Render();
        return e;
    };
    EditStateMenu.prototype.GetParent = function () {
        return this.Modeller.GetDrawer().GetElement().parentElement;
    };
    return EditStateMenu;
}(GraphModellerMenu));
/// <reference path='./index.ts'/>
/// <reference path='../../../../HTMLGenerators/index.ts'/>
var EditEdgeMenu = /** @class */ (function (_super) {
    __extends(EditEdgeMenu, _super);
    function EditEdgeMenu(modeller, edge) {
        var _this = _super.call(this, modeller) || this;
        _this.Drawing = edge;
        return _this;
    }
    EditEdgeMenu.prototype.Focus = function () {
        if (this.Element == null)
            return;
        this.SelectElement.focus();
    };
    EditEdgeMenu.prototype.GetElement = function () {
        var _this = this;
        var p = new Popup();
        p.SetCloseable(false);
        var select = document.createElement("select");
        var petrinet = Store.GetInstance().GetPetrinet();
        var currentTransition = this.Drawing.GetEdge().GetTransition();
        var transitions = petrinet.GetTransitions().sort();
        for (var i = 0; i < transitions.length; i++) {
            if (transitions[i] !== currentTransition) {
                var option = document.createElement("option");
                option.appendChild(document.createTextNode(transitions[i]));
                select.appendChild(option);
            }
        }
        select.addEventListener("keypress", function (e) {
            if (e.keyCode == 13) {
                _this.Confirm();
            }
        });
        var buttonContainer = document.createElement("div");
        var cancelButton = document.createElement("button");
        cancelButton.appendChild(document.createTextNode("Cancel"));
        cancelButton.addEventListener("click", function (e) {
            _this.Hide();
        });
        cancelButton.classList.add("cancel");
        var confirmButton = document.createElement("button");
        confirmButton.appendChild(document.createTextNode("Confirm"));
        confirmButton.addEventListener("click", function (e) {
            _this.Confirm();
        });
        confirmButton.classList.add("confirm");
        buttonContainer.appendChild(cancelButton);
        buttonContainer.appendChild(confirmButton);
        buttonContainer.classList.add("buttons");
        p.SetBody(select);
        p.AppendBody(buttonContainer);
        p.SetElementClassname("edit edge");
        this.SelectElement = select;
        return p.Render();
    };
    EditEdgeMenu.prototype.Confirm = function () {
        var a = new EditEdge(this.Modeller.GetDrawer(), this.Drawing.GetEdge(), this.SelectElement.value);
        this.Modeller.ExecuteAndStore(a);
        this.Modeller.GetDrawer().Draw();
        this.Hide();
    };
    EditEdgeMenu.prototype.GetParent = function () {
        return this.Modeller.GetDrawer().GetElement().parentElement;
    };
    return EditEdgeMenu;
}(GraphModellerMenu));
/// <reference path='./index.ts'/>
var GraphModellerContextMenu = /** @class */ (function (_super) {
    __extends(GraphModellerContextMenu, _super);
    function GraphModellerContextMenu(modeler, position, relpos) {
        var _this = _super.call(this, modeler) || this;
        _this.Position = position;
        _this.RelativePosition = relpos;
        return _this;
    }
    GraphModellerContextMenu.prototype.Focus = function () {
        if (this.Element != null)
            this.Element.focus();
    };
    GraphModellerContextMenu.prototype.GetElement = function () {
        var _this = this;
        var modeler = this.Modeller;
        var menu = new ContextMenu(this.Position.X, this.Position.Y);
        menu.Add("Add State", function () { modeler.AddState(_this.RelativePosition); });
        if (modeler.SelectedId != null) {
            var graph = Store.GetInstance().GetGraph();
            if (graph.ContainsState(modeler.SelectedId)) {
                menu.Add("Set Initial", modeler.SetInitial.bind(modeler));
            }
            menu.Add("Edit Element", modeler.EditElementMenu.bind(modeler));
            menu.Add("Remove Element", modeler.RemoveElement.bind(modeler));
            var settings = Settings.GetInstance();
            if (settings.GetDebug()) {
                menu.Add(modeler.SelectedId.toString(), function () {
                    console.log(modeler.Feedback.SpecificItems.get(modeler.SelectedId).GetCodes().sort());
                });
            }
        }
        return menu.Render();
    };
    GraphModellerContextMenu.prototype.GetParent = function () {
        return this.Modeller.GetDrawer().GetElement().parentElement;
    };
    return GraphModellerContextMenu;
}(GraphModellerMenu));
/// <reference path='./GraphModellerMenu.ts'/>
/// <reference path='./StateMenu.ts'/>
/// <reference path='./EditStateMenu.ts'/>
/// <reference path='./EditEdgeMenu.ts'/>
/// <reference path='./ContextMenu.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='./index.ts'/>
var WorkspaceBoundAction = /** @class */ (function () {
    function WorkspaceBoundAction(ws) {
        this.Workspace = ws;
    }
    return WorkspaceBoundAction;
}());
/// <reference path='./index.ts'/>
/// <reference path='../RequestStation/index.ts'/>
/// <reference path='../ResponseInterpreter/index.ts'/>
var RequestingAction = /** @class */ (function () {
    function RequestingAction() {
        this.SubInterpreters = [];
    }
    RequestingAction.prototype.Attach = function (irp) {
        this.SubInterpreters.push(irp);
    };
    RequestingAction.prototype.Detach = function (irp) {
        var index = this.SubInterpreters.indexOf(irp);
        if (index >= 0) {
            this.SubInterpreters.removeAt(index);
        }
    };
    RequestingAction.prototype.ReceiveBusy = function () {
        this.PerformBusy();
        var subs = this.SubInterpreters;
        for (var i = 0; i < subs.length; i++) {
            subs[i].ReceiveBusy();
        }
    };
    RequestingAction.prototype.ReceiveSuccess = function (code, responseText) {
        this.PerformSuccess(code, responseText);
        var subs = this.SubInterpreters;
        for (var i = 0; i < subs.length; i++) {
            subs[i].ReceiveSuccess(code, responseText);
        }
    };
    RequestingAction.prototype.ReceiveFailure = function (code, responseText) {
        this.PerformFailure(code, responseText);
        var subs = this.SubInterpreters;
        for (var i = 0; i < subs.length; i++) {
            subs[i].ReceiveFailure(code, responseText);
        }
    };
    return RequestingAction;
}());
/// <reference path='./index.ts'/>
/// <reference path='../Modules/index.ts'/>
var InitModeller = /** @class */ (function (_super) {
    __extends(InitModeller, _super);
    function InitModeller() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    InitModeller.prototype.Invoke = function () {
        var split = new SplitContainer();
        var left = document.createElement("div");
        var rightdrawer = new GraphDrawer(1000, 1000);
        var right = new GraphModeller(rightdrawer);
        var settings = Settings.GetInstance();
        settings.Attach(rightdrawer);
        settings.Attach(right);
        split.SetLeft(left);
        split.SetRight(rightdrawer);
        var store = Store.GetInstance();
        var pid = store.GetPetrinetId();
        var header = this.GenerateHeader();
        var m = new Menu();
        var toggle = this.GenerateToggleButton();
        toggle.addEventListener('click', function () { m.Toggle(); });
        window.addEventListener("keypress", function (e) {
            if (e.charCode == 77 || e.charCode == 109) {
                m.Toggle();
                toggle.classList.toggle('active');
            }
        });
        var container = this.Workspace.Container;
        container.appendChild(header);
        container.appendChild(m.Render());
        container.appendChild(toggle);
        container.appendChild(split.Render());
        right.GenerateButtons();
        rightdrawer.Resize();
        RequestStation.GetPetrinetImage(new PetrinetImager(left), pid);
        split.Resize();
        right.ToggleTutorial();
    };
    InitModeller.prototype.GenerateHeader = function () {
        var h = document.createElement("header");
        var title = document.createElement("h1");
        title.appendChild(document.createTextNode("CORA"));
        h.appendChild(title);
        return h;
    };
    InitModeller.prototype.GenerateToggleButton = function () {
        var toggle = document.createElement("div");
        for (var i = 0; i < 3; i++) {
            var bar = document.createElement('div');
            bar.classList.add('bar');
            toggle.appendChild(bar);
        }
        toggle.setAttribute("id", "menuToggle");
        toggle.addEventListener('click', function () {
            toggle.classList.toggle('active');
        });
        return toggle;
    };
    InitModeller.prototype.GenerateTutorialPopup = function () {
        var t = new Tutorial(document.body);
        return t.Render();
    };
    return InitModeller;
}(WorkspaceBoundAction));
/// <reference path='./index.ts'/>
/// <reference path='../Response/index.ts'/>
var GetPetrinet = /** @class */ (function (_super) {
    __extends(GetPetrinet, _super);
    function GetPetrinet() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    GetPetrinet.prototype.Invoke = function () {
        var store = Store.GetInstance();
        var pid = store.GetPetrinetId();
        RequestStation.GetPetrinet(this, pid);
    };
    GetPetrinet.prototype.PerformSuccess = function (code, responseText) {
        var response = JSON.parse(responseText);
        var places = response.places;
        var trans = response.transitions;
        var store = Store.GetInstance();
        var petrinet = new Petrinet(places, trans);
        store.SetPetrinet(petrinet);
    };
    GetPetrinet.prototype.PerformFailure = function (code, responseText) {
        try {
            console.warn("Could not get Petri net");
        }
        catch (e) {
            alert(e);
        }
    };
    GetPetrinet.prototype.PerformBusy = function () {
        console.log("getting Petri net...");
    };
    return GetPetrinet;
}(RequestingAction));
/// <reference path='./index.ts'/>
var RegisterPetrinet = /** @class */ (function (_super) {
    __extends(RegisterPetrinet, _super);
    function RegisterPetrinet(fd) {
        var _this = _super.call(this) || this;
        if (fd != null) {
            _this.FormData = fd;
        }
        return _this;
    }
    RegisterPetrinet.prototype.Invoke = function () {
        RequestStation.RegisterPetrinet(this, this.FormData);
    };
    RegisterPetrinet.prototype.PerformSuccess = function (code, responseText) {
        var response = JSON.parse(responseText);
        var store = Store.GetInstance();
        store.SetPetrinetId(response.petrinetId);
    };
    RegisterPetrinet.prototype.PerformFailure = function (code, responseText) {
        console.warn("Petri net registration failed");
    };
    RegisterPetrinet.prototype.PerformBusy = function () {
        console.log("registering Petri net");
    };
    RegisterPetrinet.prototype.SetFormData = function (fd) {
        this.FormData = fd;
    };
    return RegisterPetrinet;
}(RequestingAction));
/// <reference path='./index.ts'/>
var RegisterUser = /** @class */ (function (_super) {
    __extends(RegisterUser, _super);
    function RegisterUser(fd) {
        if (fd === void 0) { fd = null; }
        var _this = _super.call(this) || this;
        _this.FormData = fd;
        return _this;
    }
    RegisterUser.prototype.Invoke = function () {
        if (this.FormData != null) {
            RequestStation.RegisterUser(this, this.FormData);
        }
        else {
            console.log("Could not register user: parameter unknown");
        }
    };
    RegisterUser.prototype.PerformSuccess = function (code, responseText) {
        try {
            var response = JSON.parse(responseText);
            var store = Store.GetInstance();
            store.SetUserId(response.id);
        }
        catch (e) {
            console.log(responseText);
        }
    };
    RegisterUser.prototype.PerformFailure = function (code, responseText) {
        try {
            console.warn("registration failed");
        }
        catch (e) {
            alert(e);
        }
    };
    RegisterUser.prototype.PerformBusy = function () {
        console.log("registering user...");
    };
    RegisterUser.prototype.SetFormData = function (fd) {
        this.FormData = fd;
    };
    return RegisterUser;
}(RequestingAction));
/// <reference path='./index.ts'/>
var GetSession = /** @class */ (function (_super) {
    __extends(GetSession, _super);
    function GetSession() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    GetSession.prototype.Invoke = function () {
        var store = Store.GetInstance();
        if (store.GetPetrinetId() != null && store.GetUserId() != null) {
            var uid = store.GetUserId();
            var pid = store.GetPetrinetId();
            RequestStation.SetSession(this, uid, pid);
        }
    };
    GetSession.prototype.PerformSuccess = function (code, responseText) {
        try {
            var s = JSON.parse(responseText);
            var store = Store.GetInstance();
            store.SetSessionId(s.session_id);
        }
        catch (e) {
            console.warn(e);
        }
    };
    GetSession.prototype.PerformFailure = function (code, responseText) {
        try {
            var e = JSON.parse(responseText);
            console.log(e);
        }
        catch (e) {
            console.warn(e);
        }
    };
    GetSession.prototype.PerformBusy = function () {
        console.log("setting session...");
    };
    return GetSession;
}(RequestingAction));
/// <reference path='./Action.ts'/>
/// <reference path='./UndoableAction.ts'/>
/// <reference path='./WorkspaceBoundAction.ts'/>
/// <reference path='./RequestingAction.ts'/>
/// <reference path='./InitModeller.ts'/>
/// <reference path='./GetPetrinet.ts'/>
/// <reference path='./RegisterPetrinet.ts'/>
/// <reference path='./RegisterUser.ts'/>
/// <reference path='./GetSession.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../../../../Action/index.ts'/>
var GraphModellerAction = /** @class */ (function () {
    function GraphModellerAction(m) {
        this.Drawer = m;
    }
    GraphModellerAction.ElementCounter = 0;
    return GraphModellerAction;
}());
var StateAction = /** @class */ (function (_super) {
    __extends(StateAction, _super);
    function StateAction() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    StateAction.prototype.ParseStateString = function (stateString, id) {
        var k = id != null ? id : GraphModellerAction.ElementCounter;
        var state = new State(k);
        var s = stateString.replace(/\s/g, '');
        if (s != "") {
            var pairs = s.split(',');
            for (var i = 0; i < pairs.length; i++) {
                var pair = pairs[i];
                var k_1 = pair.split(':');
                var place = k_1[0];
                var tokens = parseInt(k_1[1], 10);
                var t = void 0;
                if (isNaN(tokens)) {
                    t = new OmegaToken();
                }
                else {
                    t = new IntToken(tokens);
                }
                state.Add(place, t);
            }
        }
        if (id == null)
            GraphModellerAction.ElementCounter++;
        return state;
    };
    return StateAction;
}(GraphModellerAction));
/// <reference path='./index.ts'/>
var AddState = /** @class */ (function (_super) {
    __extends(AddState, _super);
    function AddState(drawer, s, position, id) {
        var _this = _super.call(this, drawer) || this;
        _this.StateString = s;
        _this.AssignedId = id;
        _this.Position = position;
        return _this;
    }
    AddState.prototype.Invoke = function () {
        var state = this.ParseStateString(this.StateString, this.AssignedId);
        var graph = Store.GetInstance().GetGraph();
        graph.AddState(state);
        var drawer = this.Drawer;
        var p = this.Position != null ? this.Position : new Point(0, 0);
        var drawing = new StateDrawing(this.Drawer, state, p);
        drawer.AddStateDrawing(drawing);
        this.AssignedId = state.GetId();
    };
    AddState.prototype.Undo = function () {
        var drawer = this.Drawer;
        this.Position = drawer.GetStateDrawing(this.AssignedId).GetPosition();
        var action = new RemoveState(this.Drawer, this.AssignedId);
        action.Invoke();
    };
    return AddState;
}(StateAction));
/// <reference path='./GraphModellerAction.ts'/>
var RemoveState = /** @class */ (function (_super) {
    __extends(RemoveState, _super);
    function RemoveState(modeller, id) {
        var _this = _super.call(this, modeller) || this;
        _this.StateId = id;
        _this.StateCopy = undefined;
        _this.Position = undefined;
        _this.FromNeighbours = [];
        _this.ToNeighbours = [];
        _this.InitialState = false;
        return _this;
    }
    RemoveState.prototype.Undo = function () {
        var a = new AddState(this.Drawer, this.StateCopy.ToSystemString(), this.Position, this.StateId);
        a.Invoke();
        if (this.InitialState) {
            var graph = Store.GetInstance().GetGraph();
            graph.SetInitialState(this.StateId);
        }
        for (var i = 0; i < this.FromNeighbours.length; i++) {
            var e = new AddEdge(this.Drawer, this.StateCopy, this.FromNeighbours[i].GetToState(), this.FromNeighbours[i].GetTransition(), this.FromNeighbours[i].GetId());
            e.Invoke();
        }
        for (var i = 0; i < this.ToNeighbours.length; i++) {
            var e = new AddEdge(this.Drawer, this.ToNeighbours[i].GetFromState(), this.StateCopy, this.ToNeighbours[i].GetTransition(), this.ToNeighbours[i].GetId());
            e.Invoke();
        }
    };
    RemoveState.prototype.Invoke = function () {
        var drawer = this.Drawer;
        var graph = Store.GetInstance().GetGraph();
        var state = graph.GetStates().get(this.StateId);
        var initial = graph.GetInitialState(); //.GetId();
        if (initial && initial.GetId() == this.StateId) {
            this.InitialState = true;
            graph.SetInitialState(undefined);
        }
        var drawing = drawer.GetStateDrawing(state.GetId());
        this.StateCopy = state;
        this.Position = drawing.GetPosition();
        this.FromNeighbours = graph.GetFromNeighbours(state);
        this.ToNeighbours = graph.GetToNeighbours(state);
        graph.RemoveState(state);
        drawer.RemoveStateDrawing(this.StateId);
    };
    return RemoveState;
}(GraphModellerAction));
/// <reference path='./index.ts'/>
var EditState = /** @class */ (function (_super) {
    __extends(EditState, _super);
    function EditState(drawer, old, _new) {
        var _this = _super.call(this, drawer) || this;
        _this.OldState = old;
        if (typeof _new == "string") {
            _new = _this.ParseStateString(_new, old.GetId());
        }
        _this.NewState = _new;
        return _this;
    }
    EditState.prototype.Invoke = function () {
        var drawer = this.Drawer;
        var graph = Store.GetInstance().GetGraph();
        graph.ReplaceState(this.OldState, this.NewState);
        var drawing = drawer.GetStateDrawing(this.OldState.GetId());
        drawing.SetState(this.NewState);
    };
    EditState.prototype.Undo = function () {
        var drawer = this.Drawer;
        var graph = Store.GetInstance().GetGraph();
        graph.ReplaceState(this.NewState, this.OldState);
        var drawing = drawer.GetStateDrawing(this.NewState.GetId());
        drawing.SetState(this.OldState);
    };
    return EditState;
}(StateAction));
/// <reference path='./index.ts'/>
var SetInitialState = /** @class */ (function (_super) {
    __extends(SetInitialState, _super);
    function SetInitialState(drawer, id) {
        var _this = _super.call(this, drawer) || this;
        _this.StateId = id;
        return _this;
    }
    SetInitialState.prototype.Invoke = function () {
        var graph = Store.GetInstance().GetGraph();
        this.OldInitial = graph.GetInitialState() ? graph.GetInitialState().GetId() : undefined;
        graph.SetInitialState(this.StateId);
    };
    SetInitialState.prototype.Undo = function () {
        var graph = Store.GetInstance().GetGraph();
        graph.SetInitialState(this.OldInitial);
    };
    return SetInitialState;
}(StateAction));
/// <reference path='./index.ts'/>
/// <reference path='../../../index.ts'/>
/// <reference path='../../../../Systems/index.ts'/>
var AddEdge = /** @class */ (function (_super) {
    __extends(AddEdge, _super);
    function AddEdge(drawer, from, to, transition, id) {
        var _this = _super.call(this, drawer) || this;
        var graph = Store.GetInstance().GetGraph();
        if (typeof from == "number") {
            from = graph.GetState(from);
        }
        if (typeof to == "number") {
            to = graph.GetState(to);
        }
        _this.From = from;
        _this.To = to;
        _this.Transition = transition;
        _this.EdgeId = id;
        return _this;
    }
    AddEdge.prototype.Invoke = function () {
        var graph = Store.GetInstance().GetGraph();
        var id = this.EdgeId == null ? GraphModellerAction.ElementCounter : this.EdgeId;
        var edge = new Edge(id, this.From, this.To, this.Transition);
        graph.AddEdge(edge);
        GraphModellerAction.ElementCounter++;
        this.EdgeId = id;
        var drawer = this.Drawer;
        var fd = drawer.GetStateDrawing(this.From.GetId());
        var td = drawer.GetStateDrawing(this.To.GetId());
        var d = this.From.equals(this.To) ?
            new SelfLoopDrawing(this.Drawer, edge, fd, this.Transition, 0) :
            new OtherEdgeDrawing(this.Drawer, edge, fd, td, this.Transition);
        drawer.AddEdgeDrawing(d);
    };
    AddEdge.prototype.Undo = function () {
        var a = new RemoveEdge(this.Drawer, this.EdgeId);
        a.Invoke();
    };
    return AddEdge;
}(GraphModellerAction));
/// <reference path='./index.ts'/>
var RemoveEdge = /** @class */ (function (_super) {
    __extends(RemoveEdge, _super);
    function RemoveEdge(drawer, edgeId) {
        var _this = _super.call(this, drawer) || this;
        _this.EdgeId = edgeId;
        return _this;
    }
    RemoveEdge.prototype.Invoke = function () {
        var drawer = this.Drawer;
        var graph = Store.GetInstance().GetGraph();
        var edge = graph.GetEdge(this.EdgeId);
        this.EdgeCopy = edge;
        var drawing = this.Drawer.GetEdgeDrawing(this.EdgeId);
        graph.RemoveEdge(this.EdgeId);
        drawer.RemoveEdgeDrawing(this.EdgeId);
    };
    RemoveEdge.prototype.Undo = function () {
        var a = new AddEdge(this.Drawer, this.EdgeCopy.GetFromState(), this.EdgeCopy.GetToState(), this.EdgeCopy.GetTransition(), this.EdgeId);
        a.Invoke();
    };
    return RemoveEdge;
}(GraphModellerAction));
/// <reference path='./GraphModellerAction.ts'/>
var EditEdge = /** @class */ (function (_super) {
    __extends(EditEdge, _super);
    function EditEdge(drawer, edge, _new) {
        var _this = _super.call(this, drawer) || this;
        var graph = Store.GetInstance().GetGraph();
        if (!(edge instanceof Edge)) {
            edge = graph.GetEdge(edge);
        }
        _this.EdgeId = edge.GetId();
        _this.OldTransition = edge.GetTransition();
        _this.NewTransition = _new;
        return _this;
    }
    EditEdge.prototype.Invoke = function () {
        var graph = Store.GetInstance().GetGraph();
        var edge = graph.GetEdge(this.EdgeId);
        var drawing = this.Drawer.GetEdgeDrawing(this.EdgeId);
        edge.SetTransition(this.NewTransition);
    };
    EditEdge.prototype.Undo = function () {
        var graph = Store.GetInstance().GetGraph();
        var edge = graph.GetEdge(this.EdgeId);
        edge.SetTransition(this.OldTransition);
    };
    return EditEdge;
}(GraphModellerAction));
/// <reference path='./GraphModellerAction.ts'/>
/// <reference path='./StateAction.ts'/>
/// <reference path='./AddState.ts'/>
/// <reference path='./RemoveState.ts'/>
/// <reference path='./EditState.ts'/>
/// <reference path='./SetInitialState.ts'/>
/// <reference path='./AddEdge.ts'/>
/// <reference path='./RemoveEdge.ts'/>
/// <reference path='./EditEdge.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>
/// <reference path='../../../../Drawings/index.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashtable.d.ts'/>
var GraphElementDrawing = /** @class */ (function (_super) {
    __extends(GraphElementDrawing, _super);
    // protected Feedback  : FeedbackRecord | undefined;
    function GraphElementDrawing(drawer) {
        var _this = _super.call(this) || this;
        _this.Drawer = drawer;
        return _this;
        // this.Feedback = undefined
    }
    return GraphElementDrawing;
}(Drawing));
/// <reference path='./index.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashset.d.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashtable.d.ts'/>
var Feedback = /** @class */ (function () {
    function Feedback() {
        this.SpecificItems = new Hashtable();
        this.GeneralItems = new HashSet();
    }
    Feedback.prototype.Contains = function (id) {
        return this.SpecificItems.containsKey(id);
    };
    Feedback.prototype.AddFeedback = function (code, id) {
        if (id != null) {
            if (!this.SpecificItems.containsKey(id)) {
                this.SpecificItems.put(id, new FeedbackRecord());
            }
            this.SpecificItems.get(id).AddCode(code);
        }
        else {
            this.GeneralItems.add(code);
        }
    };
    Feedback.prototype.GetFeedback = function (id) {
        if (this.SpecificItems.containsKey(id)) {
            return this.SpecificItems.get(id);
        }
    };
    Feedback.prototype.ClearFeedback = function (id) {
        if (id != null) {
            if (this.SpecificItems.containsKey(id)) {
                this.SpecificItems.get(id).ClearCodes();
            }
        }
        else {
            this.GeneralItems.clear();
            this.SpecificItems.clear();
        }
    };
    Feedback.JsonToFeedback = function (json) {
        var f = new Feedback();
        var j = JSON.parse(json);
        var keys = Object.keys(j);
        for (var i = 0; i < keys.length; i++) {
            if (keys[i] == "general") {
                for (var k = 0; k < j["general"].length; k++) {
                    f.AddFeedback(j["general"][k]);
                }
            }
            else if (keys[i] == "specific") {
                var indices = Object.keys(j["specific"]);
                for (var k = 0; k < indices.length; k++) {
                    var id = indices[k];
                    var codes = j["specific"][id];
                    for (var h = 0; h < codes.length; h++) {
                        f.AddFeedback(codes[h], Number(id));
                    }
                }
            }
        }
        return f;
    };
    Feedback.prototype.isEmpty = function () {
        return this.SpecificItems.isEmpty() && this.GeneralItems.isEmpty();
    };
    Feedback.prototype.print = function () {
        var general = this.GeneralItems.values();
        console.log("general:");
        var s = "";
        for (var i = 0; i < general.length; i++) {
            s += general[i];
        }
        console.log(s);
        var specific = this.SpecificItems.keys();
        console.log("specific:");
        for (var i = 0; i < specific.length; i++) {
            var k = this.SpecificItems.get(specific[i]);
            console.log(specific[i], k.Items.values());
        }
    };
    return Feedback;
}());
// type Feedback = IHashtable<number, FeedbackItem>
/// <reference path='./index.ts'/>
var FeedbackCode;
(function (FeedbackCode) {
    // initial state
    FeedbackCode[FeedbackCode["NO_INITIAL_STATE"] = 400] = "NO_INITIAL_STATE";
    FeedbackCode[FeedbackCode["INCORRECT_INITIAL_STATE"] = 401] = "INCORRECT_INITIAL_STATE";
    FeedbackCode[FeedbackCode["CORRECT_INITIAL_STATE"] = 200] = "CORRECT_INITIAL_STATE";
    // states
    FeedbackCode[FeedbackCode["REACHABLE_FROM_PRESET"] = 220] = "REACHABLE_FROM_PRESET";
    FeedbackCode[FeedbackCode["DUPLICATE_STATE"] = 320] = "DUPLICATE_STATE";
    FeedbackCode[FeedbackCode["NOT_REACHABLE_FROM_PRESET"] = 420] = "NOT_REACHABLE_FROM_PRESET";
    FeedbackCode[FeedbackCode["EDGE_MISSING"] = 421] = "EDGE_MISSING";
    FeedbackCode[FeedbackCode["NOT_REACHABLE_FROM_INITIAL"] = 422] = "NOT_REACHABLE_FROM_INITIAL";
    // edges
    FeedbackCode[FeedbackCode["ENABLED_CORRECT_POST"] = 240] = "ENABLED_CORRECT_POST";
    FeedbackCode[FeedbackCode["ENABLED_CORRECT_POST_WRONG_LABEL"] = 440] = "ENABLED_CORRECT_POST_WRONG_LABEL";
    FeedbackCode[FeedbackCode["ENABLED_INCORRECT_POST"] = 441] = "ENABLED_INCORRECT_POST";
    FeedbackCode[FeedbackCode["DISABLED"] = 442] = "DISABLED";
    FeedbackCode[FeedbackCode["DISABLED_CORRECT_POST"] = 443] = "DISABLED_CORRECT_POST";
    FeedbackCode[FeedbackCode["DUPLICATE_EDGE"] = 340] = "DUPLICATE_EDGE";
})(FeedbackCode || (FeedbackCode = {}));
/// <reference path='./index.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashset.d.ts'/>
var FeedbackRecord = /** @class */ (function () {
    function FeedbackRecord() {
        this.Items = new HashSet();
    }
    FeedbackRecord.prototype.AddCode = function (code) {
        this.Items.add(code);
    };
    FeedbackRecord.prototype.ClearCodes = function () {
        this.Items.clear();
    };
    FeedbackRecord.prototype.IsEmpty = function () {
        var b = this.Items.isEmpty();
        return b;
    };
    FeedbackRecord.prototype.GetCodes = function () {
        return this.Items.values();
    };
    FeedbackRecord.prototype.ToString = function () {
        var strings = this.ToStrings();
        var result = strings.join(", ");
        return result;
    };
    FeedbackRecord.prototype.ToStrings = function () {
        var result = [];
        var codes = this.GetCodes();
        for (var i = 0; i < codes.length; i++) {
            result.push(FeedbackTranslator.Translate(codes[i]));
        }
        return result;
    };
    return FeedbackRecord;
}());
/// <reference path='./index.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashtable.d.ts'/>
var FeedbackTranslator = /** @class */ (function () {
    function FeedbackTranslator() {
    }
    FeedbackTranslator.Translate = function (feedbackid) {
        if (FeedbackTranslator.Translations == null) {
            FeedbackTranslator.FillTranslations();
        }
        return FeedbackTranslator.Translations.get(feedbackid);
    };
    FeedbackTranslator.FillTranslations = function () {
        FeedbackTranslator.Translations = new Hashtable();
        var t = FeedbackTranslator.Translations;
        // initial states
        t.put(FeedbackCode.NO_INITIAL_STATE, "No initial state is defined");
        t.put(FeedbackCode.INCORRECT_INITIAL_STATE, "The defined initial state is incorrect");
        t.put(FeedbackCode.CORRECT_INITIAL_STATE, "The initial state is correct");
        // states
        t.put(FeedbackCode.NOT_REACHABLE_FROM_PRESET, "This state is not reachable from one of the markings in its pre-set");
        t.put(FeedbackCode.REACHABLE_FROM_PRESET, "This state is reachable");
        t.put(FeedbackCode.EDGE_MISSING, "This state is missing an outgoing edge");
        t.put(FeedbackCode.DUPLICATE_STATE, "This state occurs multiple times in the graph");
        t.put(FeedbackCode.NOT_REACHABLE_FROM_INITIAL, "This state is not reachable from the initial state of the graph");
        // edges
        t.put(FeedbackCode.ENABLED_CORRECT_POST, "This transition is enabled and points to the correct state");
        t.put(FeedbackCode.ENABLED_CORRECT_POST_WRONG_LABEL, "This edge's label is incorrect");
        t.put(FeedbackCode.ENABLED_INCORRECT_POST, "This transition does not lead to this state");
        t.put(FeedbackCode.DISABLED, "This transition can't fire");
        t.put(FeedbackCode.DISABLED_CORRECT_POST, "This transition is disabled, but this state is reachable");
        t.put(FeedbackCode.DUPLICATE_EDGE, "This transition leads to multiple states");
    };
    return FeedbackTranslator;
}());
/// <reference path='./Feedback.ts'/>
/// <reference path='./FeedbackCode.ts'/>
/// <reference path='./FeedbackRecord.ts'/>
/// <reference path='./FeedbackTranslator.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../Feedback/index.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashtable.d.ts'/>
var StyleManager = /** @class */ (function () {
    function StyleManager() {
        this.Feedback = undefined;
        this.Callbacks = new Hashtable();
        var c = this.Callbacks;
        // states
        c.put(FeedbackCode.NOT_REACHABLE_FROM_PRESET, new SetIncorrectStateStyle());
        c.put(FeedbackCode.REACHABLE_FROM_PRESET, new SetCorrectStateStyle());
        c.put(FeedbackCode.EDGE_MISSING, new SetWarningStateStyle());
        c.put(FeedbackCode.DUPLICATE_STATE, new SetWarningStateStyle());
        c.put(FeedbackCode.NOT_REACHABLE_FROM_INITIAL, new SetWarningStateStyle());
        // edges
        c.put(FeedbackCode.ENABLED_CORRECT_POST, new SetCorrectEdgeStyle());
        c.put(FeedbackCode.ENABLED_CORRECT_POST_WRONG_LABEL, new SetIncorrectEdgeStyle());
        c.put(FeedbackCode.ENABLED_INCORRECT_POST, new SetIncorrectEdgeStyle());
        c.put(FeedbackCode.DISABLED, new SetIncorrectEdgeStyle());
        c.put(FeedbackCode.DISABLED_CORRECT_POST, new SetIncorrectEdgeStyle());
        c.put(FeedbackCode.DUPLICATE_EDGE, new SetWarningEdgeStyle());
    }
    //#region Standard Styles
    // shape styles - normal
    StyleManager.SetStandardStateStyle = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = "black";
        context.lineWidth = 2;
    };
    StyleManager.SetStandardEdgeStyle = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = "black";
        context.lineWidth = 2;
    };
    StyleManager.SetStandardSelfLoopStyle = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = "black";
        context.lineWidth = 2;
    };
    // shape styles - selected
    StyleManager.SetSelectedStateStyle = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = "#7ad3ff99";
        context.lineWidth = 20;
    };
    StyleManager.SetSelectedEdgeStyle = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = "#7ad3ff99";
        context.lineWidth = 20;
    };
    StyleManager.SetSelectedSelfLoopStyle = function (context) {
        context.fillStyle = "transparent";
        context.strokeStyle = "#7ad3ff99";
        context.lineWidth = 20;
    };
    // text styles
    StyleManager.SetTextStyleState = function (context) {
        context.fillStyle = "black";
        context.font = "14pt monospace";
        context.textBaseline = "middle";
        context.textAlign = "center";
    };
    StyleManager.SetTextStyleEdge = function (context) {
        context.fillStyle = "black";
        context.font = "13pt monospace";
        context.textBaseline = "middle";
        context.textAlign = "center";
    };
    StyleManager.SetTextStyleSelfLoop = function (context) {
        context.fillStyle = "black";
        context.font = "13pt monospace";
        context.textBaseline = "middle";
        context.textAlign = "center";
    };
    //#endregion
    //#region adding feedback
    StyleManager.prototype.SetFeedback = function (feedback) {
        this.Feedback = feedback;
    };
    StyleManager.prototype.ClearFeedback = function () {
        this.Feedback = undefined;
    };
    //#endregion
    //#region Setting non-standard styles
    StyleManager.prototype.SetStateStyle = function (id, context) {
        if (this.Feedback != null && this.Feedback.Contains(id)) {
            var f = this.Feedback.GetFeedback(id);
            var codes = f.GetCodes();
            var k = codes;
            var c = Math.max.apply(Math, k);
            if (this.Callbacks.containsKey(c))
                this.Callbacks.get(c).Invoke(context);
            else
                StyleManager.SetStandardStateStyle(context);
        }
        else {
            StyleManager.SetStandardStateStyle(context);
        }
    };
    StyleManager.prototype.SetEdgeStyle = function (id, context) {
        if (this.Feedback != null && this.Feedback.Contains(id)) {
            var f = this.Feedback.GetFeedback(id);
            var codes = f.GetCodes();
            var k = codes;
            var c = Math.max.apply(Math, k);
            if (this.Callbacks.containsKey(c)) {
                this.Callbacks.get(c).Invoke(context);
            }
            else {
                StyleManager.SetStandardEdgeStyle(context);
            }
        }
        else {
            StyleManager.SetStandardEdgeStyle(context);
        }
    };
    StyleManager.prototype.SetSelfLoopStyle = function (id, context) {
        if (this.Feedback != null && this.Feedback.Contains(id)) {
            var f = this.Feedback.GetFeedback(id);
            var codes = f.GetCodes();
            var k = codes;
            var c = Math.max.apply(Math, k);
            if (this.Callbacks.containsKey(c)) {
                this.Callbacks.get(c).Invoke(context);
            }
            else {
                StyleManager.SetStandardSelfLoopStyle(context);
            }
        }
        else {
            StyleManager.SetStandardSelfLoopStyle(context);
        }
    };
    return StyleManager;
}());
/// <reference path='./index.ts'/>
/// <reference path='../../../../../Action/index.ts'/>
var StyleManagerAction = /** @class */ (function () {
    function StyleManagerAction() {
        this.Green = "#1fb20899";
        this.Red = "#ff000077";
        this.Orange = "#FC8D2ACC";
    }
    return StyleManagerAction;
}());
/// <reference path='./index.ts'/>
var SetIncorrectStateStyle = /** @class */ (function (_super) {
    __extends(SetIncorrectStateStyle, _super);
    function SetIncorrectStateStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetIncorrectStateStyle.prototype.Invoke = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = this.Red;
        context.lineWidth = 15;
    };
    return SetIncorrectStateStyle;
}(StyleManagerAction));
var SetWarningStateStyle = /** @class */ (function (_super) {
    __extends(SetWarningStateStyle, _super);
    function SetWarningStateStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetWarningStateStyle.prototype.Invoke = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = this.Orange;
        context.lineWidth = 15;
    };
    return SetWarningStateStyle;
}(StyleManagerAction));
/// <reference path='./StyleManagerAction.ts'/>
var SetCorrectStateStyle = /** @class */ (function (_super) {
    __extends(SetCorrectStateStyle, _super);
    function SetCorrectStateStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetCorrectStateStyle.prototype.Invoke = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = this.Green;
        context.lineWidth = 15;
    };
    return SetCorrectStateStyle;
}(StyleManagerAction));
var SetIncorrectEdgeStyle = /** @class */ (function (_super) {
    __extends(SetIncorrectEdgeStyle, _super);
    function SetIncorrectEdgeStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetIncorrectEdgeStyle.prototype.Invoke = function (context) {
        context.fillStyle = "white";
        context.strokeStyle = this.Red;
        context.lineWidth = 10;
    };
    return SetIncorrectEdgeStyle;
}(StyleManagerAction));
var SetWarningEdgeStyle = /** @class */ (function (_super) {
    __extends(SetWarningEdgeStyle, _super);
    function SetWarningEdgeStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetWarningEdgeStyle.prototype.Invoke = function (context) {
        context.strokeStyle = this.Orange;
        context.fillStyle = "black";
        context.lineWidth = 10;
    };
    return SetWarningEdgeStyle;
}(StyleManagerAction));
var SetCorrectEdgeStyle = /** @class */ (function (_super) {
    __extends(SetCorrectEdgeStyle, _super);
    function SetCorrectEdgeStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetCorrectEdgeStyle.prototype.Invoke = function (context) {
        context.strokeStyle = this.Green;
        context.fillStyle = "white";
        context.lineWidth = 10;
    };
    return SetCorrectEdgeStyle;
}(StyleManagerAction));
/// <reference path='./index.ts'/>
var SetCorrectSelfLoopStyle = /** @class */ (function (_super) {
    __extends(SetCorrectSelfLoopStyle, _super);
    function SetCorrectSelfLoopStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetCorrectSelfLoopStyle.prototype.Invoke = function (context) {
        context.lineWidth = 10;
        context.strokeStyle = this.Green;
        context.fillStyle = "transparent";
    };
    return SetCorrectSelfLoopStyle;
}(StyleManagerAction));
/// <reference path='./index.ts'/>
var SetWarningSelfLoopStyle = /** @class */ (function (_super) {
    __extends(SetWarningSelfLoopStyle, _super);
    function SetWarningSelfLoopStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetWarningSelfLoopStyle.prototype.Invoke = function (context) {
        context.strokeStyle = this.Orange;
        context.lineWidth = 10;
        context.fillStyle = "transparent";
    };
    return SetWarningSelfLoopStyle;
}(StyleManagerAction));
/// <reference path='./index.ts'/>
var SetIncorrectSelfLoopStyle = /** @class */ (function (_super) {
    __extends(SetIncorrectSelfLoopStyle, _super);
    function SetIncorrectSelfLoopStyle() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SetIncorrectSelfLoopStyle.prototype.Invoke = function (context) {
        context.strokeStyle = this.Red;
        context.lineWidth = 10;
        context.fillStyle = "transparent";
    };
    return SetIncorrectSelfLoopStyle;
}(StyleManagerAction));
/// <reference path='./StyleManagerAction.ts'/>
// states
/// <reference path='./SetIncorrectStateStyle.ts'/>
/// <reference path='./SetWarningStateStyle.ts'/>
/// <reference path='./SetCorrectStateStyle.ts'/>
// edges
/// <reference path='./SetIncorrectEdgeStyle.ts'/>
/// <reference path='./SetWarningEdgeStyle.ts'/>
/// <reference path='./SetCorrectEdgeStyle.ts'/>
// selfloops
/// <reference path='./SetCorrectSelfLoopStyle.ts'/>
/// <reference path='./SetWarningSelfLoopStyle.ts'/>
/// <reference path='./SetIncorrectSelfLoopStyle.ts'/>
/// <reference path='./StyleManager.ts'/>
/// <reference path='./Actions/index.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../StyleManager/index.ts'/>
var StateDrawing = /** @class */ (function (_super) {
    __extends(StateDrawing, _super);
    function StateDrawing(drawer, state, position) {
        if (position === void 0) { position = new Point(0, 0); }
        var _this = _super.call(this, drawer) || this;
        _this.State = state;
        _this.Position = position;
        return _this;
    }
    StateDrawing.prototype.GetShape = function (context) {
        var padding = Settings.GetInstance().GetStatePadding();
        context.save();
        StyleManager.SetTextStyleState(context);
        var width = context.measureText(this.State.ToDisplayString()).width + padding;
        context.restore();
        var height = Settings.GetInstance().GetStateHeight();
        var tl = new Point(this.Position.X, this.Position.Y);
        var br = new Point(this.Position.X + width, this.Position.Y + height);
        var box = new Box(tl, br);
        return box;
    };
    StateDrawing.prototype.DrawShape = function (context) {
        var shape = this.GetShape(context);
        shape.Fill(context);
        shape.Stroke(context);
        this.DrawText(context);
    };
    StateDrawing.prototype.DrawText = function (context) {
        var s = this.GetShape(context).GetSize();
        var text = this.State.ToDisplayString();
        var wh = s.Width / 2;
        var hh = s.Height / 2;
        context.save();
        StyleManager.SetTextStyleState(context);
        context.fillText(text, this.Position.X + wh, this.Position.Y + hh);
        context.restore();
    };
    StateDrawing.prototype.MoveTo = function (position) {
        this.Position = position;
    };
    //#region Getters and Setters
    StateDrawing.prototype.GetState = function () {
        return this.State;
    };
    StateDrawing.prototype.SetState = function (state) {
        this.State = state;
    };
    StateDrawing.prototype.GetPosition = function () {
        return this.Position;
    };
    StateDrawing.prototype.GetSize = function (context) {
        var shape = this.GetShape(context);
        return shape.GetSize();
    };
    return StateDrawing;
}(GraphElementDrawing));
/// <reference path='./index.ts'/>
/// <reference path='../../../../Drawings/index.ts'/>
var EdgeDrawing = /** @class */ (function (_super) {
    __extends(EdgeDrawing, _super);
    function EdgeDrawing(drawer, edge, from, trans) {
        var _this = _super.call(this, drawer) || this;
        _this.Edge = edge;
        _this.From = from;
        return _this;
    }
    //#region Getters and Setters
    EdgeDrawing.prototype.GetEdge = function () {
        return this.Edge;
    };
    EdgeDrawing.prototype.GetFromDrawing = function () {
        return this.From;
    };
    return EdgeDrawing;
}(GraphElementDrawing));
/// <reference path='./index.ts'/>
/// <reference path='../../../../Shapes/index.ts'/>
/// <reference path='../../../../Systems/index.ts'/>
/// <reference path='../../../../Utils/Datastructures/Point/Point.ts'/>
var OtherEdgeDrawing = /** @class */ (function (_super) {
    __extends(OtherEdgeDrawing, _super);
    function OtherEdgeDrawing(drawer, edge, from, to, transition) {
        var _this = _super.call(this, drawer, edge, from, transition) || this;
        _this.Curvature = 0;
        _this.To = to;
        _this.Curvature = 0;
        return _this;
    }
    OtherEdgeDrawing.prototype.GetShape = function (context) {
        context.save();
        StyleManager.SetStandardStateStyle(context);
        var fromShape = this.From.GetShape(context);
        var toShape = this.To.GetShape(context);
        context.restore();
        var fromPos = fromShape.StartPoint;
        var fromSize = fromShape.GetSize();
        var toPos = toShape.StartPoint;
        var toSize = toShape.GetSize();
        var angleTo = Math.calcAngle(toPos, fromPos);
        var angleFrom = Math.calcAngle(fromPos, toPos);
        var bt = fromShape.GetBoundingPoint(angleFrom);
        var bf = toShape.GetBoundingPoint(angleTo);
        var shape = new Arrow(bt, bf);
        shape.TipHeight = 18;
        shape.TipWidth = 10;
        shape.Curvature = this.Curvature;
        return shape;
    };
    OtherEdgeDrawing.prototype.DrawShape = function (context) {
        var shape = this.GetShape(context);
        shape.Fill(context);
        this.DrawText(context);
    };
    OtherEdgeDrawing.prototype.DrawText = function (context) {
        var shape = this.GetShape(context);
        var s = this.From.GetSize(context);
        var p = this.From.GetPosition();
        var point = shape.GetCurveCutPoint();
        context.save();
        StyleManager.SetTextStyleEdge(context);
        // get font height
        var reg = context.font.match(/^[0-9]+/i);
        var textHeight = 0;
        var heightPadding = 10;
        if (reg) {
            textHeight = Number(reg[0]) + heightPadding;
        }
        var textWidth = context.measureText(this.Edge.GetTransition()).width;
        context.beginPath();
        context.clearRect(point.X - textWidth / 2, point.Y - textHeight / 2, textWidth, textHeight);
        context.fillText(this.Edge.GetTransition(), point.X, point.Y);
        context.closePath();
        context.restore();
    };
    //#region Getters and Setters
    OtherEdgeDrawing.prototype.GetToDrawing = function () {
        return this.To;
    };
    OtherEdgeDrawing.prototype.GetCurvature = function () {
        return this.Curvature;
    };
    OtherEdgeDrawing.prototype.SetCurvature = function (curv) {
        this.Curvature = curv;
    };
    return OtherEdgeDrawing;
}(EdgeDrawing));
/// <reference path='./index.ts'/>
var SelfLoopDrawing = /** @class */ (function (_super) {
    __extends(SelfLoopDrawing, _super);
    function SelfLoopDrawing(drawer, edge, from, trans, dir) {
        if (dir === void 0) { dir = 0.25; }
        var _this = _super.call(this, drawer, edge, from, trans) || this;
        _this.Direction = dir;
        return _this;
    }
    SelfLoopDrawing.prototype.MoveTo = function (position) {
        var t = Math.calcAngle(this.GetFromDrawing().GetPosition(), position);
        this.SetDirection(t);
    };
    SelfLoopDrawing.prototype.GetShape = function (context) {
        var drawing = this.From;
        var pos = drawing.GetPosition();
        var r = Settings.GetInstance().GetEdgeRadius();
        var bt = this.From.GetShape(context).GetBoundingPoint(this.Direction);
        var center = bt;
        var shape = new Circle(center, r, 0, Math.PI * 2);
        return shape;
    };
    SelfLoopDrawing.prototype.DrawShape = function (context) {
        var shape = this.GetShape(context);
        shape.Stroke(context);
        this.DrawText(context);
    };
    SelfLoopDrawing.prototype.DrawText = function (context) {
        // this.SetTextStyle(context);        
        context.save();
        StyleManager.SetStandardStateStyle(context);
        var shape = this.From.GetShape(context);
        context.restore();
        var r = Settings.GetInstance().GetEdgeRadius();
        var p = this.From.GetPosition();
        var point = shape.GetBoundingPoint(this.Direction);
        point.X += r * Math.cos(this.Direction);
        point.Y -= r * Math.sin(this.Direction);
        // get font height
        context.save();
        StyleManager.SetTextStyleSelfLoop(context);
        var reg = context.font.match(/^[0-9]+/i);
        var textHeight = 0;
        var heightPadding = 10;
        if (reg) {
            textHeight = Number(reg[0]) + heightPadding;
        }
        var textWidth = context.measureText(this.Edge.GetTransition()).width;
        context.beginPath();
        context.clearRect(point.X - textWidth / 2, point.Y - textHeight / 2, textWidth, textHeight);
        context.fillText(this.Edge.GetTransition(), point.X, point.Y);
        context.closePath();
        context.restore();
    };
    SelfLoopDrawing.prototype.SetDirection = function (dir) {
        this.Direction = dir % (Math.PI * 2);
    };
    SelfLoopDrawing.prototype.GetPosition = function () {
        return this.From.GetPosition();
    };
    return SelfLoopDrawing;
}(EdgeDrawing));
/// <reference path='./GraphElementDrawing.ts'/>
/// <reference path='./StateDrawing.ts'/>
/// <reference path='./EdgeDrawing.ts'/>
/// <reference path='./OtherEdgeDrawing.ts'/>
/// <reference path='./SelfLoopDrawing.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../Actions/index.ts'/>
var HistoryList = /** @class */ (function () {
    function HistoryList(actions) {
        this.Current = -1;
        this.Last = 0;
        this.Items = [];
    }
    HistoryList.prototype.Add = function (a) {
        this.Current += 1;
        this.Last = this.Current;
        this.Items[this.Current] = a;
    };
    HistoryList.prototype.Undo = function () {
        if (this.Current >= 0) {
            var a = this.Items[this.Current];
            a.Undo();
        }
        this.Current = Math.max(-1, this.Current - 1);
    };
    HistoryList.prototype.Redo = function () {
        if (this.Current < this.Last) {
            this.Current++;
            var a = this.Items[this.Current];
            a.Invoke();
        }
    };
    HistoryList.prototype.IsEmpty = function () {
        return this.Current < 0;
    };
    return HistoryList;
}());
/// <reference path='./HistoryList.ts'/>
/// <reference path='../../../../HTMLGenerators/index.ts'/>
var AddStateButton = /** @class */ (function (_super) {
    __extends(AddStateButton, _super);
    function AddStateButton() {
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "ADD_STATE_BUTTON";
        _this.ElementId = "addState";
        return _this;
    }
    AddStateButton.prototype.GenerateElement = function () {
        var button = document.createElement("button");
        button.appendChild(document.createTextNode("Add State"));
        this.AddClassname(button, "modeler add");
        return button;
    };
    return AddStateButton;
}(HTMLGenerator));
/// <reference path='./index.ts'/>
/// <reference path='../Feedback/index.ts'/>
var FeedbackButton = /** @class */ (function (_super) {
    __extends(FeedbackButton, _super);
    function FeedbackButton() {
        var _this = _super.call(this) || this;
        _this.ClassPrefix = "FEEDBACK_BUTTON";
        return _this;
    }
    FeedbackButton.prototype.SetBusy = function () {
        this.SetText(FeedbackButton.BusyText);
    };
    FeedbackButton.prototype.SetClear = function () {
        this.SetText(FeedbackButton.ClearText);
    };
    FeedbackButton.prototype.SetSubmit = function () {
        this.SetText(FeedbackButton.SubmitText);
    };
    FeedbackButton.prototype.SetFeedback = function (f) {
        this.Feedback = f;
        if (f.isEmpty()) {
            this.SetSubmit();
        }
        else {
            this.SetClear();
        }
    };
    FeedbackButton.prototype.SetText = function (s) {
        if (this.Element != null) {
            this.Element.innerText = s;
        }
    };
    FeedbackButton.prototype.GenerateElement = function () {
        var b = document.createElement("button");
        var t = "Submit";
        b.appendChild(document.createTextNode(t));
        this.AddClassname(b, "modeler feedback");
        return b;
    };
    FeedbackButton.SubmitText = "Submit";
    FeedbackButton.BusyText = "Getting Feedback";
    FeedbackButton.ClearText = "Clear Feedback";
    return FeedbackButton;
}(HTMLGenerator));
/// <reference path='./index.ts'/>
/// <reference path='../Feedback/index.ts'/>
var FeedbackContainer = /** @class */ (function (_super) {
    __extends(FeedbackContainer, _super);
    function FeedbackContainer(container, f, id) {
        var _this = _super.call(this) || this;
        if (container != null) {
            _this.Container = container;
        }
        if (f != null) {
            _this.Feedback = f;
        }
        if (id != null) {
            _this.DrawingId = id;
        }
        _this.ClassPrefix = "FEEDBACK_CONTAINER";
        _this.ElementId = "feedbackContainer";
        return _this;
    }
    FeedbackContainer.prototype.Display = function (container) {
        if (container != null) {
            this.Container = container;
        }
        if (this.Container == null) {
            return;
        }
        if (this.Element != null) {
            this.Remove();
        }
        var element = this.Render(true);
        this.Container.appendChild(element);
    };
    FeedbackContainer.prototype.GenerateElement = function () {
        if (this.Feedback == null)
            return;
        var f = this.Feedback;
        var container = document.createElement("div");
        var g = f.GeneralItems;
        if (g.contains(FeedbackCode.NO_INITIAL_STATE) || g.contains(FeedbackCode.INCORRECT_INITIAL_STATE)) {
            var generalContainer = document.createElement("div");
            generalContainer.classList.add("category");
            var codes = g.values().sort();
            for (var i = 0; i < codes.length; i++) {
                var item = this.RenderGeneralItem(codes[i]);
                generalContainer.appendChild(item);
            }
            container.appendChild(generalContainer);
        }
        var s = f.SpecificItems;
        if (this.DrawingId != null && s.containsKey(this.DrawingId)) {
            var specificContainer = document.createElement("div");
            specificContainer.classList.add("category");
            var record = s.get(this.DrawingId);
            var codes = record.GetCodes();
            codes.sort();
            for (var i = 0; i < codes.length; i++) {
                var item = this.RenderSpecifItem(codes[i]);
                specificContainer.appendChild(item);
            }
            container.appendChild(specificContainer);
        }
        return container;
    };
    FeedbackContainer.prototype.RenderGeneralItem = function (code) {
        var c = document.createElement("div");
        c.classList.add("item");
        var t = FeedbackTranslator.Translate(code);
        c.appendChild(document.createTextNode(t));
        return c;
    };
    FeedbackContainer.prototype.RenderSpecifItem = function (code) {
        var c = document.createElement("div");
        c.classList.add("item");
        var text = FeedbackTranslator.Translate(code);
        c.appendChild(document.createTextNode(text));
        return c;
    };
    FeedbackContainer.prototype.SetFeedback = function (f) {
        // console.log("setting feedback");
        this.Feedback = f;
        if (this.Element != null) {
            this.Display();
        }
    };
    FeedbackContainer.prototype.SetElementId = function (id) {
        this.DrawingId = id;
        if (this.Element != null) {
            this.Display();
        }
    };
    return FeedbackContainer;
}(HTMLGenerator));
/// <reference path='./index.ts'/>
/// <reference path='../../../../HTMLGenerators/index.ts'/>
var Tutorial = /** @class */ (function (_super) {
    __extends(Tutorial, _super);
    function Tutorial(c) {
        var _this = _super.call(this) || this;
        _this.Container = c;
        return _this;
    }
    Tutorial.prototype.Show = function () {
        this.Hide();
        var e = this.Render();
        this.Container.appendChild(e);
    };
    Tutorial.prototype.Hide = function () {
        if (this.Element != null) {
            this.Element.remove();
        }
        this.Element = null;
    };
    Tutorial.prototype.Toggle = function () {
        if (this.Element != null) {
            this.Hide();
        }
        else {
            this.Show();
        }
    };
    Tutorial.prototype.GenerateElement = function () {
        var popup = new Popup();
        popup.SetCloseable(true);
        popup.SetTitle("Tutorial");
        popup.SetClassnameDialog("popup tutorial");
        var pbody = document.createElement("div");
        var pgen = new ParagraphBuilder();
        pgen.Add("On the left you see an image of the Petri net you selected. This right window is the modeler, which you'll use to model a coverability graph belonging to this Petri net.");
        pbody.appendChild(pgen.Render());
        pgen.Clear();
        pgen.Add("You can activate this tutorial again by pressing ");
        pgen.Add(this.GenerateKey("h"));
        pbody.appendChild(pgen.Render());
        var subheader = document.createElement("h2");
        subheader.appendChild(document.createTextNode("Manipulating the Graph"));
        pbody.appendChild(subheader);
        pgen.Clear();
        pgen.Add("You can add states by pressing ");
        pgen.Add(this.GenerateKey("a"));
        pgen.Add(', or by pressing the "Add State" button.');
        pbody.appendChild(pgen.Render());
        pgen.Clear();
        pgen.Add("Select an edge or state by clicking on it.");
        pbody.appendChild(pgen.Render());
        pgen.Clear();
        pgen.Add("Open a context menu by right-clicking.");
        pbody.appendChild(pgen.Render());
        pgen.Clear();
        pgen.Add("Set an initial state by selecting a state and pressing ");
        pgen.Add(this.GenerateKey("i"));
        pgen.Add(".");
        pgen.Clear();
        pgen.Add("You can add an edge between two states by selecting one state and selecting another while holding ");
        pgen.Add(this.GenerateKey("CTRL"));
        pgen.Add(".");
        pbody.appendChild(pgen.Render());
        pgen.Clear();
        pgen.Add("You can edit states by double clicking on them or be pressing ");
        pgen.Add(this.GenerateKey("e"));
        pgen.Add(".");
        pbody.appendChild(pgen.Render());
        pgen.Clear();
        pgen.Add("By pressing ");
        pgen.Add(this.GenerateKey("CTRL-z"));
        pgen.Add(" and ");
        pgen.Add(this.GenerateKey("CTRL-y"));
        pgen.Add(" you can undo and redo your actions");
        pbody.appendChild(pgen.Render());
        pgen.Clear();
        pgen.Add("When you have a state or edge selected, pressing ");
        pgen.Add(this.GenerateKey("DEL"));
        pgen.Add(" deletes this item.");
        pbody.appendChild(pgen.Render());
        popup.SetBody(pbody);
        return popup.Render();
    };
    Tutorial.prototype.GenerateKey = function (key) {
        var span = document.createElement("span");
        span.classList.add("key");
        span.appendChild(document.createTextNode(key));
        return span;
    };
    return Tutorial;
}(HTMLGenerator));
/// <reference path='./AddStateButton.ts'/>
/// <reference path='./FeedbackButton.ts'/>
/// <reference path='./FeedbackContainer.ts'/>
/// <reference path='./Tutorial.ts'/>
/// <reference path='./GraphDrawer.ts'/>
/// <reference path='./GraphModeller.ts'/>
/// <reference path='./Menus/index.ts'/>
/// <reference path='./Actions/index.ts'/>
/// <reference path='./Drawings/index.ts'/>
/// <reference path='./Utils/index.ts'/>
/// <reference path='./Feedback/index.ts'/>
/// <reference path='./StyleManager/index.ts'/>
/// <reference path='./Elements/index.ts'/>
/// <reference path='./Modeller/index.ts'/>
/// <reference path='./GraphModeller/index.ts'/>
/// <reference path='../../HTMLGenerators/MenuBuilder/index.ts'/>
/// <reference path='../../Models/Settings.ts'/>
var Menu = /** @class */ (function (_super) {
    __extends(Menu, _super);
    function Menu() {
        var _this = _super.call(this) || this;
        var settings = Settings.GetInstance();
        var gridSettingsCat = "Grid Options";
        var snapToGrid = new Switcher(function () { settings.SetSnapGrid(true); }, function () { settings.SetSnapGrid(false); }, Number(settings.GetSnapGrid()));
        var displayGrid = new Switcher(function () { settings.SetDisplayGrid(true); }, function () { settings.SetDisplayGrid(false); }, Number(settings.GetDisplayGrid()));
        _this.AddMenuItem(gridSettingsCat, "SnapGrid", new MenuItem("Snap to Grid", snapToGrid));
        _this.AddMenuItem(gridSettingsCat, "DisplayGrid", new MenuItem("Display Grid", displayGrid));
        var displaySettingsCat = "Graph Options";
        var stateStyle = new Switcher(function () { settings.SetStateDisplayStyle(StateDisplayStyle.FULL); }, function () { settings.SetStateDisplayStyle(StateDisplayStyle.NON_NEGATIVE); }, Number(settings.GetStateDisplayStyle()));
        stateStyle.OffLabel = "Minimal";
        stateStyle.OnLabel = "Full";
        _this.AddMenuItem(displaySettingsCat, "StateDisplayStyle", new MenuItem("State Display Style", stateStyle));
        var feedbackSettingsCat = "Feedback Options";
        var difficultySetting = new Switcher(function () { settings.SetDifficulty(ModelingDifficulty.ADVANCED); }, function () { settings.SetDifficulty(ModelingDifficulty.NOVICE); }, Number(settings.GetDifficulty()));
        difficultySetting.OffLabel = "Immediate";
        difficultySetting.OnLabel = "On Request";
        _this.AddMenuItem(feedbackSettingsCat, "Difficulty", new MenuItem("Feedback Style", difficultySetting));
        settings.Attach(_this);
        return _this;
    }
    return Menu;
}(MenuBuilder));
/// <reference path='./Menu.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../../Observer/index.ts'/>
/// <reference path='../../ResponseInterpreter/index.ts'/>
/// <reference path='../../Utils/Tools/SVG/Parser.ts'/>
var PetrinetImager = /** @class */ (function () {
    function PetrinetImager(container) {
        this.Container = container;
        this.SubInterpreters = [];
    }
    PetrinetImager.prototype.Attach = function (irp) {
        this.SubInterpreters.push(irp);
    };
    PetrinetImager.prototype.Detach = function (irp) {
        var index = this.SubInterpreters.indexOf(irp);
        if (index >= 0) {
            this.SubInterpreters.removeAt(index);
        }
    };
    PetrinetImager.prototype.ReceiveBusy = function () {
        this.Container.innerHTML = "";
        this.Container.style.position = "relative;";
        var loader = document.createElement("div");
        loader.classList.add("loader");
        loader.classList.add("absolute_center");
        this.Container.appendChild(loader);
        for (var i = 0; i < this.SubInterpreters.length; i++) {
            this.SubInterpreters[i].ReceiveBusy();
        }
    };
    PetrinetImager.prototype.ReceiveFailure = function (code, responseText) {
        console.log(responseText);
        var subs = this.SubInterpreters;
        for (var i = 0; i < subs.length; i++) {
            this.SubInterpreters[i].ReceiveFailure(code, responseText);
        }
    };
    PetrinetImager.prototype.ReceiveSuccess = function (code, responseText) {
        var img;
        try {
            img = SVGParser.ParseSvg(JSON.parse(responseText));
            img.classList.add("petrinetSVG");
            img.style.height = "100%";
        }
        catch (e) {
            img = document.createElement("div");
            img.appendChild(document.createTextNode("Could not get an image of the Petri net"));
            img.style.position = "absolute";
            img.style.top = "50%";
        }
        img.style.display = "block";
        img.style.fontFamily = "sans";
        this.Container.innerHTML = "";
        this.Container.appendChild(img);
        var subs = this.SubInterpreters;
        for (var i = 0; i < subs.length; i++) {
            subs[i].ReceiveSuccess(code, responseText);
        }
    };
    return PetrinetImager;
}());
/// <reference path='./PetrinetImager.ts'/>
/// <reference path='../Action/index.ts'/>
var Workspace = /** @class */ (function () {
    function Workspace() {
    }
    Workspace.prototype.Work = function () {
        if (this.Action) {
            this.Action.Invoke();
            this.Action = undefined;
        }
    };
    Workspace.prototype.SetAction = function (action) {
        this.Action = action;
        this.Work();
    };
    return Workspace;
}());
/// <reference path='./index.ts'/>
/// <reference path='../Action/index.ts'/>
/// <reference path='../HTMLGenerators/index.ts'/>
var DialogWorkspace = /** @class */ (function (_super) {
    __extends(DialogWorkspace, _super);
    function DialogWorkspace() {
        var _this = _super.call(this) || this;
        _this.Dialog = new Dialog();
        return _this;
    }
    return DialogWorkspace;
}(Workspace));
/// <reference path='./index.ts'/>
var ContainerizedWorkspace = /** @class */ (function (_super) {
    __extends(ContainerizedWorkspace, _super);
    function ContainerizedWorkspace(container) {
        if (container === void 0) { container = document.body; }
        var _this = _super.call(this) || this;
        _this.Container = container;
        return _this;
    }
    return ContainerizedWorkspace;
}(Workspace));
/// <reference path='./Workspace.ts'/>
/// <reference path='./DialogWorkspace.ts'/>
/// <reference path='./ContainerizedWorkspace.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../../Action/index.ts'/>
/// <reference path='../../Workspace/index.ts'/>
/// <reference path='../../Models/Store.ts'/>
var Initializer = /** @class */ (function () {
    function Initializer() {
    }
    Initializer.prototype.Update = function (store) {
        // If we do not know which user we have, we need to find out
        // by letting him/her register.
        if (!store.GetUserId()) {
            var l = new InitWorkspace();
            l.Work();
        }
        // Once the user has selected a petrinet we need to set up the
        // modelling environment.
        if (store.GetPetrinetId() != null
            && store.GetPetrinet() != null
            && store.GetSessionId() != null) {
            var container = document.getElementById("modeller");
            var m = new ModellerWorkspace(container);
            m.Work();
        }
    };
    return Initializer;
}());
/// <reference path='./index.ts'/>
/// <reference path='../Actions/index.ts'/>
/// <reference path='../../../Workspace/index.ts'/>
var InitWorkspace = /** @class */ (function (_super) {
    __extends(InitWorkspace, _super);
    function InitWorkspace() {
        var _this = _super.call(this) || this;
        _this.Dialog.SetElementClassname("dialog");
        _this.Dialog.SetTitle("");
        _this.Dialog.SetBody(document.createElement("div"));
        document.body.appendChild(_this.Dialog.Render());
        _this.SetAction(new InitRegisterUser(_this));
        return _this;
    }
    return InitWorkspace;
}(DialogWorkspace));
/// <reference path='./index.ts'/>
var ModellerWorkspace = /** @class */ (function (_super) {
    __extends(ModellerWorkspace, _super);
    function ModellerWorkspace(container) {
        var _this = _super.call(this, container) || this;
        _this.SetAction(new InitModeller(_this));
        return _this;
    }
    return ModellerWorkspace;
}(ContainerizedWorkspace));
/// <reference path='./InitWorkspace.ts'/>
/// <reference path='./ModellerWorkspace.ts'/>
/// <reference path='./index.ts'/>
/// <reference path='../Workspace/index.ts'/>
/// <reference path='../../../Action/index.ts'/>
var InitAction = /** @class */ (function (_super) {
    __extends(InitAction, _super);
    function InitAction() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    InitAction.prototype.SetBusy = function () {
        this.Workspace.Dialog.SetTitle("Loading...");
        var loader = document.createElement("div");
        loader.classList.add("loader");
        this.Workspace.Dialog.SetBody(loader);
    };
    InitAction.prototype.SetBody = function (bodyElements) {
        if (bodyElements.length > 0) {
            var d = this.Workspace.Dialog;
            d.SetBody(bodyElements[0]);
            for (var i = 1; i < bodyElements.length; i++) {
                d.AppendBody(bodyElements[i]);
            }
        }
    };
    InitAction.prototype.SetError = function (error, bodyElements) {
        var e = document.createElement("p");
        e.appendChild(document.createTextNode(error.error));
        e.classList.add("error");
        this.Workspace.Dialog.SetTitle("Something went wrong...");
        if (bodyElements != null) {
            bodyElements.unshift(e);
            this.SetBody(bodyElements);
        }
        else {
            this.SetBody([e]);
        }
    };
    return InitAction;
}(WorkspaceBoundAction));
/// <reference path='./index.ts'/>
var InitRequestingAction = /** @class */ (function (_super) {
    __extends(InitRequestingAction, _super);
    function InitRequestingAction(w) {
        var _this = _super.call(this, w) || this;
        _this.SubInterpreters = [];
        return _this;
    }
    InitRequestingAction.prototype.ReceiveBusy = function () {
        this.PerformBusy();
        var subs = this.SubInterpreters;
        for (var i = 0; i < subs.length; i++) {
            subs[i].ReceiveBusy();
        }
    };
    InitRequestingAction.prototype.ReceiveSuccess = function (code, responseText) {
        this.PerformSuccess(code, responseText);
        var subs = this.SubInterpreters;
        for (var i = 0; i < subs.length; i++) {
            subs[i].ReceiveBusy();
        }
    };
    InitRequestingAction.prototype.ReceiveFailure = function (code, responseText) {
        this.PerformFailure(code, responseText);
        var subs = this.SubInterpreters;
        for (var i = 0; i < subs.length; i++) {
            subs[i].ReceiveBusy();
        }
    };
    InitRequestingAction.prototype.Attach = function (irp) {
        this.SubInterpreters.push(irp);
    };
    InitRequestingAction.prototype.Detach = function (irp) {
        var index = this.SubInterpreters.indexOf(irp);
        if (index >= 0) {
            this.SubInterpreters.removeAt(index);
        }
    };
    return InitRequestingAction;
}(InitAction));
/// <reference path='./index.ts'/>
/// <reference path='../../../Action/index.ts'/>
var InitRegisterUser = /** @class */ (function (_super) {
    __extends(InitRegisterUser, _super);
    function InitRegisterUser() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    InitRegisterUser.prototype.Invoke = function () {
        var d = this.Workspace.Dialog;
        d.SetTitle("Welcome");
        var body = this.GetBody();
        this.SetBody(body);
    };
    InitRegisterUser.prototype.SendForm = function (fd) {
        var action = new RegisterUser(fd);
        action.Attach(this);
        action.Invoke();
    };
    InitRegisterUser.prototype.PerformBusy = function () {
        this.SetBusy();
    };
    InitRegisterUser.prototype.PerformSuccess = function (code, responseText) {
        try {
            var nextAction = new InitRegisterPetrinetAction(this.Workspace);
            this.Workspace.SetAction(nextAction);
        }
        catch (e) {
            console.log(responseText);
        }
    };
    InitRegisterUser.prototype.PerformFailure = function (code, responseText) {
        try {
            var response = JSON.parse(responseText);
            var body = this.GetBody();
            this.SetError(response, body);
        }
        catch (e) {
            console.log(responseText);
        }
    };
    InitRegisterUser.prototype.GetBody = function () {
        var _this = this;
        var p = document.createElement("p");
        p.appendChild(document.createTextNode("In order to use this website, please pick a unique username."));
        var form = FormFactory.GetUserRegistrationForm();
        var button = document.createElement("button");
        button.classList.add("confirm");
        button.appendChild(document.createTextNode("Continue"));
        button.addEventListener("click", function (e) {
            _this.SendForm(new FormData(form));
        });
        var res = [p, form, button];
        return res;
    };
    return InitRegisterUser;
}(InitRequestingAction));
/// <reference path='../HTMLGenerators/FormBuilder/index.ts'/>
var FormFactory = /** @class */ (function () {
    function FormFactory() {
    }
    FormFactory.GetUserRegistrationForm = function () {
        var builder = new FormBuilder(false);
        builder.AddLabel("Name", "name");
        builder.AddInput("name", "text", "your username");
        return builder.Render();
    };
    FormFactory.GetPetrinetRegistrationForm = function () {
        var builder = new FormBuilder(false);
        builder.AddLabel("Petri net", "petrinet");
        builder.AddInput("petrinet", "file");
        return builder.Render();
    };
    return FormFactory;
}());
/// <reference path='./index.ts'/>
/// <reference path='../../../Factories/FormFactory.ts'/>
/// <reference path='../../../ResponseInterpreter/index.ts'/>
var InitRegisterPetrinetAction = /** @class */ (function (_super) {
    __extends(InitRegisterPetrinetAction, _super);
    function InitRegisterPetrinetAction() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    InitRegisterPetrinetAction.prototype.Invoke = function () {
        this.Workspace.Dialog.SetTitle("Alright. Now the Petri net!");
        var body = this.GetBody();
        this.SetBody(body);
    };
    InitRegisterPetrinetAction.prototype.SendForm = function (fd) {
        var action = new RegisterPetrinet(fd);
        action.Attach(this);
        action.Invoke();
    };
    InitRegisterPetrinetAction.prototype.PerformBusy = function () {
        this.SetBusy();
    };
    InitRegisterPetrinetAction.prototype.PerformSuccess = function (code, responseText) {
        var nextAction = new InitGetPetrinet(this.Workspace);
        this.Workspace.SetAction(nextAction);
    };
    InitRegisterPetrinetAction.prototype.PerformFailure = function (code, responseText) {
        var error = JSON.parse(responseText);
        this.SetError(error, this.GetBody());
    };
    InitRegisterPetrinetAction.prototype.GetBody = function () {
        var _this = this;
        var p = document.createElement("p");
        p.appendChild(document.createTextNode("You are now succesfully registered. Upload a Petri net so we can get started."));
        var form = FormFactory.GetPetrinetRegistrationForm();
        var button = document.createElement("button");
        button.classList.add("confirm");
        button.appendChild(document.createTextNode("Upload"));
        button.addEventListener("click", function (e) {
            _this.SendForm(new FormData(form));
        });
        var res = [p, form, button];
        return res;
    };
    return InitRegisterPetrinetAction;
}(InitRequestingAction));
/// <reference path='./index.ts'/>
/// <reference path='../../../Action/index.ts'/>
var InitGetPetrinet = /** @class */ (function (_super) {
    __extends(InitGetPetrinet, _super);
    function InitGetPetrinet() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    InitGetPetrinet.prototype.Invoke = function () {
        var action = new GetPetrinet();
        action.Attach(this);
        action.Invoke();
    };
    InitGetPetrinet.prototype.PerformBusy = function () {
        this.SetBusy();
    };
    InitGetPetrinet.prototype.PerformSuccess = function (code, responseText) {
        var nextAction = new InitGetSession(this.Workspace);
        this.Workspace.SetAction(nextAction);
    };
    InitGetPetrinet.prototype.PerformFailure = function (code, responseText) {
        var response = JSON.parse(responseText);
        var body = this.GetErrorBody();
        this.SetError(response, body);
    };
    InitGetPetrinet.prototype.GetErrorBody = function () {
        var p = document.createElement("p");
        p.appendChild(document.createTextNode("The system could not fetch the petrinet. "));
        p.appendChild(document.createTextNode("Contact an administator"));
        return [p];
    };
    return InitGetPetrinet;
}(InitRequestingAction));
/// <reference path='./index.ts'/>
var InitGetSession = /** @class */ (function (_super) {
    __extends(InitGetSession, _super);
    function InitGetSession() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    InitGetSession.prototype.Invoke = function () {
        var action = new GetSession();
        action.Attach(this);
        action.Invoke();
    };
    InitGetSession.prototype.PerformBusy = function () {
        this.SetBusy();
    };
    InitGetSession.prototype.PerformSuccess = function (code, responseText) {
        try {
            this.Workspace.Dialog.Remove();
        }
        catch (e) {
            console.warn(e);
        }
    };
    InitGetSession.prototype.PerformFailure = function (code, responseText) {
        try {
            var e = JSON.parse(responseText);
            var p = document.createElement("p");
            p.appendChild(document.createTextNode("Please contact an administrator"));
            this.SetError(e, [p]);
        }
        catch (e) {
            console.warn(e);
        }
    };
    return InitGetSession;
}(InitRequestingAction));
/// <reference path='./InitAction.ts'/>
/// <reference path='./InitRequestingAction.ts'/>
/// <reference path='./InitRegisterUser.ts'/>
/// <reference path='./InitRegisterPetrinet.ts'/>
/// <reference path='./InitGetPetrinet.ts'/>
/// <reference path='./InitGetSession.ts'/>
/// <reference path='./Initializer.ts'/>
/// <reference path='./Actions/index.ts'/>
/// <reference path='./Workspace/index.ts'/>
/// <reference path='./Modellers/index.ts'/>
/// <reference path='./Menu/index.ts'/>
/// <reference path='./PetrinetImager/index.ts'/>
/// <reference path='./Initializer/index.ts'/>
/// <reference path='./Converters/index.ts'/>
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
var Main = /** @class */ (function () {
    function Main() {
    }
    Main.Main = function () {
        var apiPath = "";
        var settings = Settings.GetInstance();
        settings.SetApiPath(apiPath);
        var init = new Initializer();
        var store = Store.GetInstance();
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
    };
    return Main;
}());
window.addEventListener("DOMContentLoaded", function (e) {
    Main.Main();
});
// window.onbeforeunload = function() {return true;};
