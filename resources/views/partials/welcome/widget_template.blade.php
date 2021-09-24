<thead>
            <tr>
            @foreach ($headerArray as $head)
                <th>{{ $head }}</th>
            @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($valueArray as $key=>$value)
                @if(!empty($value["_bg_color"]))
                    <tr style="background-color:{{$value['_bg_color']}}">
                @else
                    <tr>
                @endif
                    @foreach($value as $k=>$val)
                        @if($k !== '_bg_color')
                            @if(is_array($val))
                                @php
                                    $bg_color = isset($val['_bg_color'])? $val['_bg_color']: "";
                                    $color = isset($val['_color'])? $val['_color']: "";
                                    $value = isset($val['_value'])? $val['_value'] : "";
                                    $href = isset($val['_href'])? $val['_href']: "";
                                    $title = isset($val['_title'])? $val['_title']: "";
                                @endphp
                                
                                @if(is_array($href))
                                    <td>
                                        @foreach($href as $kh=>$h)
                                            <a title="{{$title}}" href="{{$h}}" target="_blank">{!!$value!!}</a>
                                        @endforeach
                                    </td>
                                @elseif($href != "")
                                <td>
                                    <a title="{{$title}}" href="{{$href}}" target="_blank">{!!$value!!}</a>
                                </td>
                                @else
                                <td style="background-color: {{$bg_color}}; color: {{$color}}">{!!$value!!}</td>
                                @endif
                            @elseif($k == 'live_status_color')
                            <td><span style="border-radius: 25px;padding: 6px;" class="font-color-green live_status_{{$val}}"></span><label style="display: none;">{{$val}}</label></td>
                            @else
                                <td>{!! $val !!}</td>
                            @endif
                        @endif
                    @endforeach
                    </tr>
            @endforeach
        </tbody>

